<x-mail::message>
# 邀請您共同規劃旅程！

您的朋友 **{{ $inviter->name }}** 邀請您加入 **「{{ $trip->name }}」** 的規劃行列。

透過共同協作，您可以：
*   即時新增與調整行程細節。
*   同步記錄每一筆旅途支出。
*   勾選共同的必買與必訪清單。

<x-mail::button :url="route('trip.accept_invitation', ['token' => $token])">
接受邀請並加入
</x-mail::button>

如果您尚未註冊帳號，點擊上方連結後將引導您完成註冊，隨後即可自動加入此旅程。

祝 旅途愉快！

**ElkTrip 旅程規劃家**
</x-mail::message>
