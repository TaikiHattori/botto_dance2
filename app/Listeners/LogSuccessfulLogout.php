<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;//Carbonクラスをインポート

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        $user->last_logout_at = now();

        // ログアウト時にアプリ累計使用時間を更新
        if ($user->last_logout_at){

            //使用時間を秒数で計算
            //Carbon::parse()メソッドで文字列をCarbonインスタンスに変換
            $loginTime = Carbon::parse($user->last_login_at);
            $logoutTime = Carbon::parse($user->last_logout_at);
            //diffInSecondsメソッドで「日時の差（$logoutTime-$loginTime）」を秒単位で計算
            //※日時の差計算時、普通の引き算は避けた方が良い
            $usageTimeInSeconds = $loginTime->diffInSeconds($logoutTime);
        
            //今までの累計使用時間（00：00：00）を秒数に変換
            $currrentTotalUsageTime = $user->total_usage_time;
            list($hours, $minutes, $seconds) = sscanf($currrentTotalUsageTime, '%d:%d:%d');
            $currentTotalUsageTimeInSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

            //全累計使用時間を計算
            $totalUsageTimeInSeconds = $usageTimeInSeconds + $currentTotalUsageTimeInSeconds;
        
            //秒数を「時：分：秒」に変換
            $hours = floor($totalUsageTimeInSeconds / 3600);
            $minutes = floor(($totalUsageTimeInSeconds % 3600) / 60);
            $seconds = $totalUsageTimeInSeconds % 60;
            $totalUsageTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
            $user->total_usage_time = $totalUsageTime;
        }

        $user->save();
    }
}
