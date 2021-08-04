const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/js/app.js', 'public/assets/default/js')

    // scss
    .sass('resources/sass/app.scss', 'public/assets/default/css')
    .sass('resources/sass/panel.scss', 'public/assets/default/css')
    .sass('resources/sass/rtl-app.scss', 'public/assets/default/css')

// scripts
.babel('resources/js/parts/main.js', 'public/assets/default/js/parts/main.min.js')
.babel('resources/js/parts/home.js', 'public/assets/default/js/parts/home.min.js')
.babel('resources/js/parts/webinar_show.js', 'public/assets/default/js/parts/webinar_show.min.js')
.babel('resources/js/parts/comment.js', 'public/assets/default/js/parts/comment.min.js')
.babel('resources/js/parts/time-counter-down.js', 'public/assets/default/js/parts/time-counter-down.min.js')
.babel('resources/js/parts/navbar.js', 'public/assets/default/js/parts/navbar.min.js')
.babel('resources/js/parts/certificate_validation.js', 'public/assets/default/js/parts/certificate_validation.min.js')
.babel('resources/js/parts/cart.js', 'public/assets/default/js/parts/cart.min.js')
.babel('resources/js/parts/payment.js', 'public/assets/default/js/parts/payment.min.js')
.babel('resources/js/parts/text_lesson.js', 'public/assets/default/js/parts/text_lesson.min.js')
.babel('resources/js/parts/top_nav_flags.js', 'public/assets/default/js/parts/top_nav_flags.min.js')
.babel('resources/js/parts/categories.js', 'public/assets/default/js/parts/categories.min.js')
.babel('resources/js/parts/contact.js', 'public/assets/default/js/parts/contact.min.js')
.babel('resources/js/parts/instructors.js', 'public/assets/default/js/parts/instructors.min.js')
.babel('resources/js/parts/quiz-start.js', 'public/assets/default/js/parts/quiz-start.min.js')
.babel('resources/js/parts/profile.js', 'public/assets/default/js/parts/profile.min.js')
.babel('resources/js/parts/img_cropit.js', 'public/assets/default/js/parts/img_cropit.min.js')
.babel('resources/js/parts/become_instructor.js', 'public/assets/default/js/parts/become_instructor.min.js')
//
.babel('resources/js/panel/public.js', 'public/assets/default/js/panel/public.min.js')
.babel('resources/js/panel/webinar.js', 'public/assets/default/js/panel/webinar.min.js')
.babel('resources/js/panel/join_webinar.js', 'public/assets/default/js/panel/join_webinar.min.js')
.babel('resources/js/panel/make_next_session.js', 'public/assets/default/js/panel/make_next_session.min.js')
.babel('resources/js/panel/quiz.js', 'public/assets/default/js/panel/quiz.min.js')
.babel('resources/js/panel/comments.js', 'public/assets/default/js/panel/comments.min.js')
.babel('resources/js/panel/user_setting.js', 'public/assets/default/js/panel/user_setting.min.js')
.babel('resources/js/panel/certificates.js', 'public/assets/default/js/panel/certificates.min.js')

.babel('resources/js/panel/financial/account.js', 'public/assets/default/js/panel/financial/account.min.js')
.babel('resources/js/panel/financial/payout.js', 'public/assets/default/js/panel/financial/payout.min.js')
.babel('resources/js/panel/financial/subscribes.js', 'public/assets/default/js/panel/financial/subscribes.min.js')
//
.babel('resources/js/panel/marketing/promotions.js', 'public/assets/default/js/panel/marketing/promotions.min.js')
.babel('resources/js/panel/marketing/special_offers.js', 'public/assets/default/js/panel/marketing/special_offers.min.js')

.babel('resources/js/panel/meeting/meeting.js', 'public/assets/default/js/panel/meeting/meeting.min.js')
.babel('resources/js/panel/meeting/reserve_meeting.js', 'public/assets/default/js/panel/meeting/reserve_meeting.min.js')
.babel('resources/js/panel/meeting/contact-info.js', 'public/assets/default/js/panel/meeting/contact-info.min.js')
.babel('resources/js/panel/meeting/join_modal.js', 'public/assets/default/js/panel/meeting/join_modal.min.js')
.babel('resources/js/panel/meeting/requests.js', 'public/assets/default/js/panel/meeting/requests.min.js')

.babel('resources/js/panel/noticeboard.js', 'public/assets/default/js/panel/noticeboard.min.js')
.babel('resources/js/panel/notifications.js', 'public/assets/default/js/panel/notifications.min.js')
.babel('resources/js/panel/quiz_list.js', 'public/assets/default/js/panel/quiz_list.min.js')
.babel('resources/js/panel/conversations.js', 'public/assets/default/js/panel/conversations.min.js')
.babel('resources/js/panel/dashboard.js', 'public/assets/default/js/panel/dashboard.min.js')


// admin
.babel('resources/js/admin/webinar.js', 'public/assets/admin/js/webinar.min.js')
.babel('resources/js/admin/quiz.js', 'public/assets/default/js/admin/quiz.min.js')
.babel('resources/js/admin/contact_us.js', 'public/assets/default/js/admin/contact_us.min.js')
.babel('resources/js/admin/appointments.js', 'public/assets/default/js/admin/appointments.min.js')
.babel('resources/js/admin/categories.js', 'public/assets/default/js/admin/categories.min.js')
.babel('resources/js/admin/certificates.js', 'public/assets/default/js/admin/certificates.min.js')
.babel('resources/js/admin/comments.js', 'public/assets/default/js/admin/comments.min.js')
.babel('resources/js/admin/contacts.js', 'public/assets/default/js/admin/contacts.min.js')
.babel('resources/js/admin/filters.js', 'public/assets/default/js/admin/filters.min.js')
.babel('resources/js/admin/noticeboards.js', 'public/assets/default/js/admin/noticeboards.min.js')
.babel('resources/js/admin/notifications.js', 'public/assets/default/js/admin/notifications.min.js')
.babel('resources/js/admin/reports.js', 'public/assets/default/js/admin/reports.min.js')
.babel('resources/js/admin/reviews.js', 'public/assets/default/js/admin/reviews.min.js')
.babel('resources/js/admin/roles.js', 'public/assets/default/js/admin/roles.min.js')
.babel('resources/js/admin/settings/account_types.js', 'public/assets/default/js/admin/settings/account_types.min.js')
.babel('resources/js/admin/settings/site_bank_accounts.js', 'public/assets/default/js/admin/settings/site_bank_accounts.min.js')
.babel('resources/js/admin/settings/general_basic.js', 'public/assets/default/js/admin/settings/general_basic.min.js')
.babel('resources/js/admin/user_edit.js', 'public/assets/default/js/admin/user_edit.min.js')
.babel('resources/js/admin/webinar_reports.js', 'public/assets/default/js/admin/webinar_reports.min.js')
;
