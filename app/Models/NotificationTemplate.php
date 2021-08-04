<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $templateKeys = [
        'email' => '[u.email]',
        'mobile' => '[u.mobile]',
        'real_name' => '[u.name]',
        'instructor_name' => '[instructor.name]',
        'student_name' => '[student.name]',
        'group_title' => '[u.g.title]',
        'badge_title' => '[u.b.title]',
        'course_title' => '[c.title]',
        'quiz_title' => '[q.title]',
        'quiz_result' => '[q.result]',
        'support_ticket_title' => '[s.t.title]',
        'contact_us_title' => '[c.u.title]',
        'time_and_date' => '[time.date]',
        'link' => '[link]',
        'rate_count' => '[rate.count]',
        'amount' => '[amount]',
        'payout_account' => '[payout.account]',
        'financial_doc_desc' => '[f.d.description]',
        'financial_doc_type' => '[f.d.type]',
        'subscribe_plan_name' => '[s.p.name]',
        'promotion_plan_name' => '[p.p.name]',
    ];

    static $notificationTemplateAssignSetting = [
        'admin' => ['new_comment_admin', 'support_message_admin', 'support_message_replied_admin', 'promotion_plan_admin', 'new_contact_message','payout_request_admin'],
        'user' => ['new_badge', 'change_user_group'],
        'course' => ['course_created', 'course_approve', 'course_reject', 'new_comment', 'support_message', 'support_message_replied', 'new_rating', 'webinar_reminder'],
        'financial' => ['new_financial_document', 'payout_request', 'payout_proceed', 'offline_payment_request', 'offline_payment_approved', 'offline_payment_rejected'],
        'sale_purchase' => ['new_sales', 'new_purchase'],
        'plans' => ['new_subscribe_plan', 'promotion_plan'],
        'appointment' => ['new_appointment', 'new_appointment_link', 'appointment_reminder', 'meeting_finished'],
        'quiz' => ['new_certificate', 'waiting_quiz', 'waiting_quiz_result']
    ];
}
