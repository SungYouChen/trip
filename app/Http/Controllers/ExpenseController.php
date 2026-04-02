<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(User $user, Trip $trip)
    {
        $data = $this->getExpenseData($trip);
        $data['user'] = $user;
        $data['trip'] = $trip;
        return view('expenses.index', $data);
    }

    public function indexShared($token)
    {
        $trip = Trip::where('share_token', $token)->where('is_public', true)->firstOrFail();
        $data = $this->getExpenseData($trip);
        $data['isShared'] = true;
        $data['user'] = $trip->user;
        $data['trip'] = $trip;
        return view('expenses.index', $data);
    }

    private function getExpenseData(Trip $trip)
    {
        $expenses = Expense::where('trip_id', $trip->id)
            ->orderBy('date', 'desc')
            ->get();
            
        $totalBase = 0;
        $totalTarget = 0;
        
        $tripBase = $trip->base_currency;
        $tripTarget = $trip->target_currency;
        $rate = max($trip->exchange_rate, 0.0001);

        foreach ($expenses as $expense) {
            if ($expense->is_base_currency) {
                $totalBase += $expense->amount;
                $totalTarget += $expense->amount / $rate;
            } else {
                $totalTarget += $expense->amount;
                $totalBase += $expense->amount * $rate;
            }
        }

        $byCategory = $expenses->groupBy('category')->map(function ($group) use ($rate) {
            return $group->sum(function ($expense) use ($rate) {
                return $expense->is_base_currency ? $expense->amount : ($expense->amount * $rate);
            });
        })->toArray(); // Ensure it's an array for easy updates

        $trip->load('days');
        
        $parsePrice = function($text) use ($tripBase, $tripTarget) {
            $text = trim($text);
            if (!$text) return null;
            if (preg_match('/^([^\d]+)?([\d,.]+)/u', $text, $matches)) {
                $amount = (float) str_replace(',', '', $matches[2] ?? 0);
                $currencyRaw = trim($matches[1] ?? '');
                
                // Standardize common symbols
                $currency = $currencyRaw;
                if (in_array($currencyRaw, ['$', 'TWD', 'NT', 'NTD'])) $currency = $tripBase;
                if (in_array($currencyRaw, ['¥', 'JPY', 'JP'])) $currency = $tripTarget;
                
                if (empty($currency) && preg_match('/([\d,.]+)([^\d]+)$/u', $text, $suffixMatch)) {
                    $currency = trim($suffixMatch[2]);
                    if (in_array($currency, ['$', 'TWD', 'NT', 'NTD'])) $currency = $tripBase;
                    if (in_array($currency, ['¥', 'JPY', 'JP'])) $currency = $tripTarget;
                }
                
                return [
                    'amount' => $amount, 
                    'currency' => $currency ?: $tripBase, 
                    'raw' => trim($currencyRaw . ' ' . number_format($amount))
                ];
            }
            return null;
        };

        $externalCosts = [];
        $externalTotals = [];

        $flightPrice = $trip->flight_info['price'] ?? null;
        if ($parsed = $parsePrice($flightPrice)) {
            $externalCosts[] = ['name' => '航班機票', 'raw' => $parsed['raw']];
            $externalTotals[$parsed['currency']] = ($externalTotals[$parsed['currency']] ?? 0) + $parsed['amount'];
            
            // Add to byCategory (Flight -> Transport)
            $catAmount = ($parsed['currency'] == $tripBase) ? $parsed['amount'] : ($parsed['amount'] * $rate);
            $byCategory['Transport'] = ($byCategory['Transport'] ?? 0) + $catAmount;
        }

        foreach ($trip->days as $day) {
            $accDetails = $day->accommodation_details ?? [];
            $accPrice = $accDetails['price'] ?? null;
            if ($parsed = $parsePrice($accPrice)) {
                $dateStr = \Carbon\Carbon::parse($day->date)->format('m/d');
                $desc = $dateStr . ' 住宿' . (!empty($accDetails['name']) ? ': ' . $accDetails['name'] : '');
                $externalCosts[] = ['name' => $desc, 'raw' => $parsed['raw']];
                $externalTotals[$parsed['currency']] = ($externalTotals[$parsed['currency']] ?? 0) + $parsed['amount'];
                
                // Add to byCategory (Accommodation)
                $catAmount = ($parsed['currency'] == $tripBase) ? $parsed['amount'] : ($parsed['amount'] * $rate);
                $byCategory['Accommodation'] = ($byCategory['Accommodation'] ?? 0) + $catAmount;
            }
        }

        // Add external totals to the main totals
        foreach ($externalTotals as $currency => $amount) {
            if ($currency == $tripBase) {
                $totalBase += $amount;
                $totalTarget += $amount / $rate;
            } else {
                $totalTarget += $amount;
                $totalBase += $amount * $rate;
            }
        }

        return compact('trip', 'expenses', 'totalBase', 'totalTarget', 'byCategory', 'externalCosts', 'externalTotals');
    }

    public function store(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'date' => 'required|date',
            'is_base_currency' => 'nullable',
        ]);

        $validated['is_base_currency'] = $request->boolean('is_base_currency');

        $expense = $trip->expenses()->create($validated);

        if ($request->ajax()) {
            return response()->json([
                'message' => '花費紀錄已儲存！',
                'expense' => $expense
            ]);
        }

        return back()->with('success', '花費紀錄已儲存！');
    }

    public function update(User $user, Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'date' => 'required|date',
            'is_base_currency' => 'nullable',
        ]);

        $validated['is_base_currency'] = $request->boolean('is_base_currency');

        $expense->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => '紀錄已更新！']);
        }

        return back()->with('success', '紀錄已更新！');
    }

    public function destroy(User $user, Expense $expense)
    {
        $expense->delete(); // soft delete
        return back()->with('success', '花費已封存。');
    }

    public function restore(User $user, $expenseId)
    {
        $expense = Expense::withTrashed()->findOrFail($expenseId);
        $expense->restore();
        return back()->with('success', '花費已還原！');
    }

    public function forceDelete(User $user, $expenseId)
    {
        $expense = Expense::withTrashed()->findOrFail($expenseId);
        $expense->forceDelete();
        return back()->with('success', '花費已永久刪除。');
    }
}
