<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'web'], function () {

    // Admin Auth Routes
    Route::get('login', 'LoginController@showLoginForm');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout');

    Route::group(['middleware' => 'admin'], function () {

        Route::get('/', 'DashboardController@index');
        Route::get('/clear-cache', 'DashboardController@cacheClear');

        Route::group(['prefix' => 'dashboard'], function () {
            Route::post('/getSaleStatisticsData', 'DashboardController@getSaleStatisticsData');
        });

        Route::group(['prefix' => 'marketing'], function () {
            Route::get('/', 'DashboardController@marketing');
            Route::post('/getNetProfitChart', 'DashboardController@getNetProfitChartAjax');
        });

        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', 'RoleController@index');
            Route::get('/create', 'RoleController@create');
            Route::post('/store', 'RoleController@store');
            Route::get('/{id}/edit', 'RoleController@edit');
            Route::post('/{id}/update', 'RoleController@update');
            Route::get('/{id}/delete', 'RoleController@destroy');
        });

        Route::group(['prefix' => 'staffs'], function () {
            Route::get('/', 'UserController@staffs');
        });

        Route::group(['prefix' => 'students'], function () {
            Route::get('/', 'UserController@students');
            Route::get('/excel', 'UserController@exportExcelStudents');
        });

        Route::group(['prefix' => 'instructors'], function () {
            Route::get('/', 'UserController@instructors');
            Route::get('/excel', 'UserController@exportExcelInstructors');
        });

        Route::group(['prefix' => 'organizations'], function () {
            Route::get('/', 'UserController@organizations');
            Route::get('/excel', 'UserController@exportExcelOrganizations');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/create', 'UserController@create');
            Route::post('/store', 'UserController@store');
            Route::post('/search', 'UserController@search');
            Route::get('/{id}/edit', 'UserController@edit');
            Route::post('/{id}/update', 'UserController@update');
            Route::post('/{id}/updateImage', 'UserController@updateImage');
            Route::post('/{id}/updateImage', 'UserController@updateImage');
            Route::post('/{id}/financialUpdate', 'UserController@financialUpdate');
            Route::post('/{id}/occupationsUpdate', 'UserController@occupationsUpdate');
            Route::post('/{id}/badgesUpdate', 'UserController@badgesUpdate');
            Route::post('/{id}/badgesUpdate', 'UserController@badgesUpdate');
            Route::get('/{id}/deleteBadge/{badge_id}', 'UserController@deleteBadge');
            Route::get('/{id}/delete', 'UserController@destroy');
            Route::get('/{id}/acceptRequestToInstructor', 'UserController@acceptRequestToInstructor');
            Route::get('/{user_id}/impersonate', 'UserController@impersonate');

            Route::group(['prefix' => 'badges'], function () {
                Route::get('/', 'BadgesController@index');
                Route::post('/store', 'BadgesController@store');
                Route::get('/{id}/edit', 'BadgesController@edit');
                Route::post('/{id}/update', 'BadgesController@update');
                Route::get('/{id}/delete', 'BadgesController@delete');
            });

            Route::group(['prefix' => 'groups'], function () {
                Route::get('/', 'GroupController@index');
                Route::get('/create', 'GroupController@create');
                Route::post('/store', 'GroupController@store');
                Route::get('/{id}/edit', 'GroupController@edit');
                Route::post('/{id}/update', 'GroupController@update');
                Route::get('/{id}/delete', 'GroupController@destroy');
            });

            Route::group(['prefix' => 'become_instructors'], function () {
                Route::get('/', 'UserController@becomeInstructors');
                Route::get('/{id}/reject', 'UserController@rejectBecomeInstructors');
                Route::get('/{id}/delete', 'UserController@deleteBecomeInstructors');
            });
        });

        Route::group(['prefix' => 'supports'], function () {
            Route::get('/', 'SupportsController@index');
            Route::get('/create', 'SupportsController@create');
            Route::post('/store', 'SupportsController@store');
            Route::get('/{id}/edit', 'SupportsController@edit');
            Route::post('/{id}/update', 'SupportsController@update');
            Route::get('/{id}/delete', 'SupportsController@delete');

            Route::get('/{id}/close', 'SupportsController@conversationClose');
            Route::get('/{id}/conversation', 'SupportsController@conversation');
            Route::post('/{id}/conversation', 'SupportsController@storeConversation');

            Route::group(['prefix' => 'departments'], function () {
                Route::get('/', 'SupportDepartmentsController@index');
                Route::get('/create', 'SupportDepartmentsController@create');
                Route::post('/store', 'SupportDepartmentsController@store');
                Route::get('/{id}/edit', 'SupportDepartmentsController@edit');
                Route::post('/{id}/update', 'SupportDepartmentsController@update');
                Route::get('/{id}/delete', 'SupportDepartmentsController@delete');
            });
        });

        Route::group(['prefix' => 'noticeboards'], function () {
            Route::get('/', 'NoticeboardController@index');
            Route::get('/send', 'NoticeboardController@create');
            Route::post('/store', 'NoticeboardController@store');
            Route::get('{id}/edit', 'NoticeboardController@edit');
            Route::post('{id}/update', 'NoticeboardController@update');
            Route::get('{id}/delete', 'NoticeboardController@delete');
        });

        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', 'NotificationsController@index');
            Route::get('/send', 'NotificationsController@create');
            Route::post('/store', 'NotificationsController@store');
            Route::get('{id}/edit', 'NotificationsController@edit');
            Route::post('{id}/update', 'NotificationsController@update');
            Route::get('{id}/delete', 'NotificationsController@delete');
            Route::get('/mark_all_read', 'NotificationsController@markAllRead');
            Route::get('/{id}/mark_as_read', 'NotificationsController@markAsRead');

            Route::group(['prefix' => 'templates'], function () {
                Route::get('/', 'NotificationTemplatesController@index');
                Route::get('/create', 'NotificationTemplatesController@create');
                Route::post('/store', 'NotificationTemplatesController@store');
                Route::get('{id}/edit', 'NotificationTemplatesController@edit');
                Route::post('{id}/update', 'NotificationTemplatesController@update');
                Route::get('{id}/delete', 'NotificationTemplatesController@delete');
            });
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', 'CategoryController@index');
            Route::get('/create', 'CategoryController@create');
            Route::post('/store', 'CategoryController@store');
            Route::get('/{id}/edit', 'CategoryController@edit');
            Route::post('/{id}/update', 'CategoryController@update');
            Route::get('/{id}/delete', 'CategoryController@destroy');

            Route::group(['prefix' => 'trends'], function () {
                Route::get('/', 'TrendCategoriesController@index');
                Route::get('/create', 'TrendCategoriesController@create');
                Route::post('/store', 'TrendCategoriesController@store');
                Route::get('/{id}/edit', 'TrendCategoriesController@edit');
                Route::post('/{id}/update', 'TrendCategoriesController@update');
                Route::get('/{id}/delete', 'TrendCategoriesController@destroy');
            });
        });

        Route::group(['prefix' => 'filters'], function () {
            Route::get('/', 'FilterController@index');
            Route::get('/create', 'FilterController@create');
            Route::post('/store', 'FilterController@store');
            Route::get('/{id}/edit', 'FilterController@edit');
            Route::post('/{id}/update', 'FilterController@update');
            Route::get('/{id}/delete', 'FilterController@destroy');
        });

        Route::group(['prefix' => 'tags'], function () {
            Route::get('/', 'TagController@index');
            Route::get('/create', 'TagController@create');
            Route::post('/store', 'TagController@store');
            Route::get('/{id}/edit', 'TagController@edit');
            Route::post('/{id}/update', 'TagController@update');
            Route::get('/{id}/delete', 'TagController@destroy');
        });

        Route::group(['prefix' => 'comments/{page}'], function () {
            Route::get('/', 'CommentsController@index');
            Route::get('/{comment_id}/toggle', 'CommentsController@toggleStatus');
            Route::get('/{comment_id}/edit', 'CommentsController@edit');
            Route::post('/{comment_id}/update', 'CommentsController@update');
            Route::get('/{comment_id}/reply', 'CommentsController@reply');
            Route::post('/{comment_id}/reply', 'CommentsController@storeReply');
            Route::get('/{comment_id}/delete', 'CommentsController@delete');

            Route::group(['prefix' => 'reports'], function () {
                Route::get('/', 'CommentsController@reports');
                Route::get('/{id}/show', 'CommentsController@reportShow');
                Route::get('/{id}/delete', 'CommentsController@reportDelete');
            });
        });

        Route::group(['prefix' => 'reports'], function () {
            Route::get('/reasons', 'ReportsController@reasons');
            Route::post('/reasons', 'ReportsController@storeReasons');
            Route::get('/webinars', 'ReportsController@webinarsReports');
            Route::get('/webinars/{id}/delete', 'ReportsController@delete');
        });

        Route::group(['prefix' => 'webinars'], function () {
            Route::get('/', 'WebinarController@index');
            Route::get('/create', 'WebinarController@create');
            Route::post('/store', 'WebinarController@store');
            Route::get('/{id}/edit', 'WebinarController@edit');
            Route::post('/{id}/update', 'WebinarController@update');
            Route::get('/{id}/delete', 'WebinarController@destroy');
            Route::post('/search', 'WebinarController@search');
            Route::get('/excel', 'WebinarController@exportExcel');

            Route::group(['prefix' => 'features'], function () {
                Route::get('/', 'FeatureWebinarsControllers@index');
                Route::get('/create', 'FeatureWebinarsControllers@create');
                Route::post('/store', 'FeatureWebinarsControllers@store');
                Route::get('{feature_id}/{toggle}', 'FeatureWebinarsControllers@toggle');
                Route::get('/excel', 'FeatureWebinarsControllers@exportExcel');
            });
        });

        Route::group(['prefix' => 'quizzes'], function () {
            Route::get('/', 'QuizController@index');
            Route::get('/{id}/edit', 'QuizController@edit');
            Route::post('/{id}/update', 'QuizController@update');
            Route::get('/{id}/delete', 'QuizController@delete');
            Route::get('/{id}/results', 'QuizController@results');
            Route::get('/{id}/results/excel', 'QuizController@resultsExportExcel');
            Route::get('/result/{result_id}/delete', 'QuizController@resultDelete');
            Route::get('/excel', 'QuizController@exportExcel');
        });

        Route::group(['prefix' => 'quizzes-questions'], function () {
            Route::post('/store', 'QuizQuestionController@store');
            Route::get('/{id}/edit', 'QuizQuestionController@edit');
            Route::post('/{id}/update', 'QuizQuestionController@update');
            Route::get('/{id}/delete', 'QuizQuestionController@destroy');
        });


        Route::group(['prefix' => 'filters'], function () {
            Route::get('/get-by-category-id/{categoryId}', 'FilterController@getByCategoryId');
        });

        Route::group(['prefix' => 'tickets'], function () {
            Route::post('/store', 'TicketController@store');
            Route::post('/{id}/edit', 'TicketController@edit');
            Route::post('/{id}/update', 'TicketController@update');
            Route::get('/{id}/delete', 'TicketController@destroy');
        });

        Route::group(['prefix' => 'sessions'], function () {
            Route::post('/store', 'SessionController@store');
            Route::post('/{id}/edit', 'SessionController@edit');
            Route::post('/{id}/update', 'SessionController@update');
            Route::get('/{id}/delete', 'SessionController@destroy');
        });

        Route::group(['prefix' => 'files'], function () {
            Route::post('/store', 'FileController@store');
            Route::post('/{id}/edit', 'FileController@edit');
            Route::post('/{id}/update', 'FileController@update');
            Route::get('/{id}/delete', 'FileController@destroy');
        });

        Route::group(['prefix' => 'test-lesson'], function () {
            Route::post('/store', 'TextLessonsController@store');
            Route::post('/{id}/edit', 'TextLessonsController@edit');
            Route::post('/{id}/update', 'TextLessonsController@update');
            Route::get('/{id}/delete', 'TextLessonsController@destroy');
        });

        Route::group(['prefix' => 'prerequisites'], function () {
            Route::post('/store', 'PrerequisiteController@store');
            Route::post('/{id}/edit', 'PrerequisiteController@edit');
            Route::post('/{id}/update', 'PrerequisiteController@update');
            Route::get('/{id}/delete', 'PrerequisiteController@destroy');
        });

        Route::group(['prefix' => 'faqs'], function () {
            Route::post('/store', 'FAQController@store');
            Route::post('/{id}/description', 'FAQController@description');
            Route::post('/{id}/edit', 'FAQController@edit');
            Route::post('/{id}/update', 'FAQController@update');
            Route::get('/{id}/delete', 'FAQController@destroy');
        });

        Route::group(['prefix' => 'webinar-quiz'], function () {
            Route::post('/store', 'WebinarQuizController@store');
            Route::post('/{id}/edit', 'WebinarQuizController@edit');
            Route::post('/{id}/update', 'WebinarQuizController@update');
            Route::get('/{id}/delete', 'WebinarQuizController@destroy');
        });

        Route::group(['prefix' => 'certificates'], function () {
            Route::get('/', 'CertificateController@index');
            Route::get('/excel', 'CertificateController@exportExcel');

            Route::group(['prefix' => 'templates'], function () {
                Route::get('/', 'CertificateController@CertificatesTemplatesList');
                Route::get('/new', 'CertificateController@CertificatesNewTemplate');
                Route::post('/store', 'CertificateController@CertificatesTemplateStore');
                Route::get('/preview', 'CertificateController@CertificatesTemplatePreview');
                Route::get('/{template_id}/edit', 'CertificateController@CertificatesTemplatesEdit');
                Route::post('/{template_id}/update', 'CertificateController@CertificatesTemplateStore');
                Route::get('/{template_id}/delete', 'CertificateController@CertificatesTemplatesDelete');
            });
            Route::get('/{id}/download', 'CertificateController@CertificatesDownload');
        });

        Route::group(['prefix' => 'reviews'], function () {
            Route::get('/', 'ReviewsController@index');
            Route::get('/{id}/toggleStatus', 'ReviewsController@toggleStatus');
            Route::get('/{id}/show', 'ReviewsController@show');
            Route::get('/{id}/delete', 'ReviewsController@delete');
        });

        Route::group(['prefix' => 'consultants'], function () {
            Route::get('/', 'ConsultantsController@index');
            Route::get('/excel', 'ConsultantsController@exportExcel');

        });

        Route::group(['prefix' => 'appointments'], function () {
            Route::get('/', 'AppointmentsController@index');
            Route::get('/{id}/join', 'AppointmentsController@join');
            Route::get('/{id}/getReminderDetails', 'AppointmentsController@getReminderDetails');
            Route::get('/{id}/sendReminder', 'AppointmentsController@sendReminder');
            Route::get('/{id}/cancel', 'AppointmentsController@cancel');
        });

        Route::group(['prefix' => 'blog'], function () {
            Route::get('/', 'BlogController@index');
            Route::get('/create', 'BlogController@create');
            Route::post('/store', 'BlogController@store');
            Route::post('/search', 'BlogController@search');
            Route::get('/{id}/edit', 'BlogController@edit');
            Route::post('/{id}/update', 'BlogController@update');
            Route::get('/{id}/delete', 'BlogController@delete');

            Route::group(['prefix' => 'categories'], function () {
                Route::get('/', 'BlogCategoriesController@index');
                Route::post('/store', 'BlogCategoriesController@store');
                Route::get('/{id}/edit', 'BlogCategoriesController@edit');
                Route::post('/{id}/update', 'BlogCategoriesController@update');
                Route::get('/{id}/delete', 'BlogCategoriesController@delete');
            });
        });

        Route::group(['prefix' => 'financial'], function () {

            Route::group(['prefix' => 'sales'], function () {
                Route::get('/', 'SaleController@index');
                Route::get('/{id}/refund', 'SaleController@refund');
                Route::get('/{id}/invoice', 'SaleController@invoice');
                Route::get('/export', 'SaleController@exportExcel');
            });

            Route::group(['prefix' => 'payouts'], function () {
                Route::get('/', 'PayoutController@index');
                Route::get('/{id}/reject', 'PayoutController@reject');
                Route::get('/{id}/payout', 'PayoutController@payout');
            });

            Route::group(['prefix' => 'offline_payments'], function () {
                Route::get('/', 'OfflinePaymentController@index');
                Route::get('/{id}/reject', 'OfflinePaymentController@reject');
                Route::get('/{id}/approved', 'OfflinePaymentController@approved');
            });

            Route::group(['prefix' => 'discounts'], function () {
                Route::get('/', 'DiscountController@index');
                Route::get('/new', 'DiscountController@create');
                Route::post('/store', 'DiscountController@store');
                Route::get('/{id}/edit', 'DiscountController@edit');
                Route::post('/{id}/update', 'DiscountController@update');
                Route::get('/{id}/delete', 'DiscountController@destroy');
            });

            Route::group(['prefix' => 'special_offers'], function () {
                Route::get('/', 'SpecialOfferController@index');
                Route::get('/new', 'SpecialOfferController@create');
                Route::post('/store', 'SpecialOfferController@store');
                Route::get('/{id}/edit', 'SpecialOfferController@edit');
                Route::post('/{id}/update', 'SpecialOfferController@update');
                Route::get('/{id}/delete', 'SpecialOfferController@destroy');
            });

            Route::group(['prefix' => 'documents'], function () {
                Route::get('/', 'DocumentController@index');
                Route::get('/new', 'DocumentController@create');
                Route::post('/store', 'DocumentController@store');
                Route::get('/{id}/print', 'DocumentController@printer');
            });

            Route::group(['prefix' => 'subscribes'], function () {
                Route::get('/', 'SubscribesController@index');
                Route::get('/new', 'SubscribesController@create');
                Route::post('/store', 'SubscribesController@store');
                Route::get('/{id}/edit', 'SubscribesController@edit');
                Route::post('/{id}/update', 'SubscribesController@update');
                Route::get('/{id}/delete', 'SubscribesController@delete');
            });

            Route::group(['prefix' => 'promotions'], function () {
                Route::get('/', 'PromotionsController@index');
                Route::get('/new', 'PromotionsController@create');
                Route::get('/sales', 'PromotionsController@sales');
                Route::post('/store', 'PromotionsController@store');
                Route::get('/{id}/edit', 'PromotionsController@edit');
                Route::post('/{id}/update', 'PromotionsController@update');
                Route::get('/{id}/delete', 'PromotionsController@delete');
            });
        });

        Route::group(['prefix' => 'advertising'], function () {
            Route::group(['prefix' => 'banners'], function () {
                Route::get('/', 'AdvertisingBannersController@index');
                Route::get('/new', 'AdvertisingBannersController@create');
                Route::post('/store', 'AdvertisingBannersController@store');
                Route::get('/{id}/edit', 'AdvertisingBannersController@edit');
                Route::post('/{id}/update', 'AdvertisingBannersController@update');
                Route::get('/{id}/delete', 'AdvertisingBannersController@delete');
            });
        });

        Route::group(['prefix' => 'newsletters'], function () {
            Route::get('/', 'NewslettersController@index');
            Route::get('/{id}/delete', 'NewslettersController@delete');
            Route::get('/excel', 'NewslettersController@exportExcel');
        });

        Route::group(['prefix' => 'additional_page'], function () {
            Route::get('/{name}', 'AdditionalPageController@index');
            Route::post('/{name}', 'AdditionalPageController@store');

            Route::group(['prefix' => '/navbar_links'], function () {
                Route::post('/store', 'NavbarLinksSettingsController@store');
                Route::get('/{key}/edit', 'NavbarLinksSettingsController@edit');
                Route::get('/{key}/delete', 'NavbarLinksSettingsController@delete');
            });

            Route::post('/footer/store', 'AdditionalPageController@storeFooter');
        });

        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'SettingsController@index');
            Route::get('/{page}', 'SettingsController@page');
            Route::post('/{name}', 'SettingsController@store');
            Route::post('/seo_metas/store', 'SettingsController@storeSeoMetas');
            Route::post('/notifications/store', 'SettingsController@notificationsMetas');

            Route::group(['prefix' => '/socials'], function () {
                Route::post('/store', 'SettingsController@storeSocials');
                Route::get('/{key}/edit', 'SettingsController@editSocials');
                Route::get('/{key}/delete', 'SettingsController@deleteSocials');
            });

            Route::group(['prefix' => 'payment_channels'], function () {
                Route::get('/', 'PaymentChannelController@index');
                Route::get('/{id}/toggleStatus', 'PaymentChannelController@toggleStatus');
                Route::get('/{id}/edit', 'PaymentChannelController@edit');
                Route::post('/{id}/update', 'PaymentChannelController@update');
            });

            Route::post('/custom_css_js/store', 'SettingsController@storeCustomCssJs');
        });

        Route::group(['prefix' => 'testimonials'], function () {
            Route::get('/', 'TestimonialsController@index');
            Route::get('/create', 'TestimonialsController@create');
            Route::post('/store', 'TestimonialsController@store');
            Route::get('/{id}/edit', 'TestimonialsController@edit');
            Route::post('/{id}/update', 'TestimonialsController@update');
            Route::get('/{id}/delete', 'TestimonialsController@delete');
        });

        Route::group(['prefix' => 'contacts'], function () {
            Route::get('/', 'ContactController@index');
            Route::get('/{id}/reply', 'ContactController@reply');
            Route::post('/{id}/reply', 'ContactController@storeReply');
            Route::get('/{id}/delete', 'ContactController@delete');
        });

        Route::group(['prefix' => 'pages'], function () {
            Route::get('/', 'PagesController@index');
            Route::get('/create', 'PagesController@create');
            Route::post('/store', 'PagesController@store');
            Route::get('/{id}/edit', 'PagesController@edit');
            Route::post('/{id}/update', 'PagesController@update');
            Route::get('/{id}/delete', 'PagesController@delete');
            Route::get('/{id}/toggle', 'PagesController@statusTaggle');
        });
    });
});
