@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ config('app.url') }}/icon_logo.png" class="logo" alt="Logo" style="height: 48px; width: 48px; border-radius: 12px;">
<div style="margin-top: 10px; font-size: 22px; font-weight: 900; color: #9c8c7c; letter-spacing: -0.02em;">Trip Planner</div>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
