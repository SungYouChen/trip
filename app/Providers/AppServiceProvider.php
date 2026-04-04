<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS for all URLs (especially important for Cloudflare Tunnels)
        if (app()->environment('production') || env('FORCE_HTTPS', true) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // 自定義驗證信內容 (繁體中文)
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('[旅程足跡] 驗證您的電子信箱')
                ->greeting('您好，旅人 ' . ($notifiable->name ?? '') . '！')
                ->line('感謝您註冊 Trip Planner！在我們開始這場冒險之前，請點擊下方按鈕驗證您的信箱。')
                ->action('確認我的電子信箱', $url)
                ->line('若您沒有註冊此帳號，請忽略此信，無需進行任何動作。')
                ->salutation('祝，旅途愉快。');
        });

        // 自定義密碼重設信內容 (繁體中文)
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('[旅程足跡] 密碼重設請求')
                ->greeting('收到您的求救信號了！')
                ->line('我們收到您重設帳號密碼的請求。')
                ->action('立即重設我的密碼', $url)
                ->line('此連結將在 60 分鐘後失效。')
                ->line('如果您並未要求重設密碼，請忽略此信，這意味著您的帳號仍然是安全的。')
                ->salutation('照顧好您的冒險，再次出發。');
        });
    }
}
