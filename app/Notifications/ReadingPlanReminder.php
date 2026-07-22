<?php

namespace App\Notifications;

use App\Models\ReadingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReadingPlanReminder extends Notification
{
    use Queueable;

    public ReadingPlan $plan;

    public string $timing;

    public function __construct(ReadingPlan $plan, string $timing)
    {
        $this->plan = $plan;
        $this->timing = $timing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $bookTitle = $this->plan->book->title;

        if ($this->timing === '1_day_before') {
            $title = '明日が読書目標日です！';
            $body = "「{$bookTitle}」の読書目標日は明日です。少しだけでもページを開いてみませんか？";
        } elseif ($this->timing === 'on_target_date') {
            $title = '読書の進捗はいかがですか？';
            $body = "「{$bookTitle}」の目標日当日です。読み終わったら完了ボタンを押しましょう！";
        } else {
            $title = '読書計画のお知らせ';
            $body = "「{$bookTitle}」の読書計画に関するお知らせです。";
        }

        return [
            'plan_id' => $this->plan->id,
            'book_id' => $bookTitle,
            'timing' => $this->timing,
            'title' => $title,
            'body' => $body,
        ];
    }
}
