<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Panel Routes
|--------------------------------------------------------------------------
*/

Route::group(['namespace' => 'Panel', 'prefix' => 'panel', 'middleware' => ['impersonate', 'panel', 'share']], function () {

    Route::get('/', 'DashboardController@dashboard');

    Route::group(['prefix' => 'users'], function () {
        Route::post('/search', 'UserController@search');
        Route::post('/contact-info', 'UserController@contactInfo');
        Route::post('/offlineToggle', 'UserController@offlineToggle');
    });

    Route::group(['prefix' => 'webinars'], function () {
        Route::group(['middleware' => 'user.not.access'], function () {
            Route::get('/', 'WebinarController@index');
            Route::get('/new', 'WebinarController@create');
            Route::get('/invitations', 'WebinarController@invitations');
            Route::post('/store', 'WebinarController@store');
            Route::get('/{id}/step/{step?}', 'WebinarController@edit');
            Route::get('/{id}/edit', 'WebinarController@edit')->name('panel_edit_webinar');
            Route::post('/{id}/update', 'WebinarController@update');
            Route::get('/{id}/delete', 'WebinarController@destroy');
            Route::get('/{id}/duplicate', 'WebinarController@duplicate');
            Route::get('/{id}/export-students-list', 'WebinarController@exportStudentsList');
            Route::post('/order-items', 'WebinarController@orderItems');
        });

        Route::get('/organization_classes', 'WebinarController@organizationClasses');
        Route::get('/{id}/invoice', 'WebinarController@invoice');
        Route::get('/{id}/getNextSessionInfo', 'WebinarController@getNextSessionInfo');

        Route::group(['prefix' => 'purchases'], function () {
            Route::get('/', 'WebinarController@purchases');
            Route::post('/getJoinInfo', 'WebinarController@getJoinInfo');
        });

        Route::post('/search', 'WebinarController@search');

        Route::group(['prefix' => 'comments'], function () {
            Route::get('/', 'CommentController@myClassComments');
            Route::post('/store', 'CommentController@store');
            Route::post('/{id}/update', 'CommentController@update');
            Route::get('/{id}/delete', 'CommentController@destroy');
            Route::post('/{id}/reply', 'CommentController@reply');
            Route::post('/{id}/report', 'CommentController@report');
        });

        Route::get('/my-comments', 'CommentController@myComments');

        Route::group(['prefix' => 'favorites'], function () {
            Route::get('/', 'FavoriteController@index');
            Route::get('/{id}/delete', 'FavoriteController@destroy');
        });
    });

    Route::group(['prefix' => 'quizzes'], function () {
        Route::group(['middleware' => 'user.not.access'], function () {

            Route::get('/', 'QuizController@index');
            Route::get('/new', 'QuizController@create');
            Route::post('/store', 'QuizController@store');
            Route::get('/{id}/edit', 'QuizController@edit')->name('panel_edit_quiz');
            Route::post('/{id}/update', 'QuizController@update');
            Route::get('/{id}/delete', 'QuizController@destroy');

        });
        Route::get('/{id}/start', 'QuizController@start');
        Route::post('/{id}/store-result', 'QuizController@quizzesStoreResult');
        Route::get('/{quizResultId}/status', 'QuizController@status')->name('quiz_status');

        Route::get('/my-results', 'QuizController@myResults');
        Route::get('/opens', 'QuizController@opens');

        Route::get('/{quizResultId}/result', 'QuizController@showResult');

        Route::group(['prefix' => 'results'], function () {
            Route::get('/', 'QuizController@results');
            Route::get('/{quizResultId}/delete', 'QuizController@destroyQuizResult');
            Route::get('/{quizResultId}/downloadCertificate', 'CertificateController@downloadCertificate');
            Route::get('/{quizResultId}/showCertificate', 'CertificateController@showCertificate');
        });

        Route::get('/{quizResultId}/edit-result', 'QuizController@editResult');
        Route::post('/{quizResultId}/update-result', 'QuizController@updateResult');


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
        Route::post('/{id}/update', 'TicketController@update');
        Route::get('/{id}/delete', 'TicketController@destroy');
    });

    Route::group(['prefix' => 'sessions'], function () {
        Route::post('/store', 'SessionController@store');
        Route::post('/{id}/update', 'SessionController@update');
        Route::get('/{id}/delete', 'SessionController@destroy');
        Route::get('/{id}/joinToBigBlueButton', 'SessionController@joinToBigBlueButton');
    });

    Route::group(['prefix' => 'files'], function () {
        Route::post('/store', 'FileController@store');
        Route::post('/{id}/update', 'FileController@update');
        Route::get('/{id}/delete', 'FileController@destroy');
    });

    Route::group(['prefix' => 'text-lesson'], function () {
        Route::post('/store', 'TextLessonsController@store');
        Route::post('/{id}/update', 'TextLessonsController@update');
        Route::get('/{id}/delete', 'TextLessonsController@destroy');
    });

    Route::group(['prefix' => 'prerequisites'], function () {
        Route::post('/store', 'PrerequisiteController@store');
        Route::post('/{id}/update', 'PrerequisiteController@update');
        Route::get('/{id}/delete', 'PrerequisiteController@destroy');
    });

    Route::group(['prefix' => 'faqs'], function () {
        Route::post('/store', 'FAQController@store');
        Route::post('/{id}/update', 'FAQController@update');
        Route::get('/{id}/delete', 'FAQController@destroy');
    });

    Route::group(['prefix' => 'webinar-quiz'], function () {
        Route::post('/store', 'WebinarQuizController@store');
        Route::post('/{id}/update', 'WebinarQuizController@update');
        Route::get('/{id}/delete', 'WebinarQuizController@destroy');
    });


    Route::group(['prefix' => 'certificates'], function () {
        Route::get('/', 'CertificateController@lists');
        Route::get('/achievements', 'CertificateController@achievements');
    });

    Route::group(['prefix' => 'meetings'], function () {
        Route::get('/reservation', 'ReserveMeetingController@reservation');
        Route::get('/requests', 'ReserveMeetingController@requests');

        Route::get('/settings', 'MeetingController@setting')->name('meeting_setting');
        Route::post('/{id}/update', 'MeetingController@update');
        Route::post('saveTime', 'MeetingController@saveTime');
        Route::post('deleteTime', 'MeetingController@deleteTime');
        Route::post('temporaryDisableMeetings', 'MeetingController@temporaryDisableMeetings');

        Route::get('/{id}/join', 'ReserveMeetingController@join');
        Route::post('/create-link', 'ReserveMeetingController@createLink');
        Route::get('/{id}/finish', 'ReserveMeetingController@finish');
    });

    Route::group(['prefix' => 'financial'], function () {
        Route::get('/sales', 'SaleController@index');
        Route::get('/summary', 'AccountingController@index');
        Route::get('/payout', 'PayoutController@index');
        Route::post('/request-payout', 'PayoutController@requestPayout');
        Route::get('/account', 'AccountingController@account');
        Route::post('/charge', 'AccountingController@charge');

        Route::group(['prefix' => 'offline-payments'], function () {
            Route::get('/{id}/edit', 'AccountingController@account');
            Route::post('/{id}/update', 'AccountingController@updateOfflinePayment');
            Route::get('/{id}/delete', 'AccountingController@deleteOfflinePayment');
        });

        Route::get('/subscribes', 'SubscribesController@index');
        Route::post('/pay-subscribes', 'SubscribesController@pay');
    });

    Route::group(['prefix' => 'setting'], function () {
        Route::get('/step/{step?}', 'UserController@setting');
        Route::get('/', 'UserController@setting');
        Route::post('/', 'UserController@update');
        Route::post('/metas', 'UserController@storeMetas');
        Route::post('metas/{meta_id}/update', 'UserController@updateMeta');
        Route::get('metas/{meta_id}/delete', 'UserController@deleteMeta');
    });

    Route::group(['prefix' => 'support'], function () {
        Route::get('/', 'SupportsController@index');
        Route::get('/new', 'SupportsController@create');
        Route::post('/store', 'SupportsController@store');
        Route::get('{id}/conversations', 'SupportsController@index');
        Route::post('{id}/conversations', 'SupportsController@storeConversations');
        Route::get('{id}/close', 'SupportsController@close');

        Route::group(['prefix' => 'tickets'], function () {
            Route::get('/', 'SupportsController@tickets');
            Route::get('{id}/conversations', 'SupportsController@tickets');
        });
    });

    Route::group(['prefix' => 'marketing', 'middleware' => 'user.not.access'], function () {
        Route::get('/special_offers', 'SpecialOfferController@index')->name('special_offer_index');
        Route::post('/special_offers/store', 'SpecialOfferController@store');
        Route::get('/special_offers/{id}/disable', 'SpecialOfferController@disable');
        Route::get('/promotions', 'MarketingController@promotions');
        Route::post('/pay-promotion', 'MarketingController@payPromotion');
    });

    Route::group(['prefix' => 'noticeboard'], function () {
        Route::get('/', 'NoticeboardController@index');
        Route::get('/new', 'NoticeboardController@create');
        Route::post('/store', 'NoticeboardController@store');
        Route::get('/{noticeboard_id}/edit', 'NoticeboardController@edit');
        Route::post('/{noticeboard_id}/update', 'NoticeboardController@update');
        Route::get('/{noticeboard_id}/delete', 'NoticeboardController@delete');
        Route::get('/{noticeboard_id}/saveStatus', 'NoticeboardController@saveStatus');
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'NotificationsController@index');
        Route::get('/{id}/saveStatus', 'NotificationsController@saveStatus');
    });

    // organization instructor and students route
    Route::group(['prefix' => 'manage'], function () {
        Route::get('/{user_type}', 'UserController@manageUsers');
        Route::get('/{user_type}/new', 'UserController@createUser');
        Route::post('/{user_type}/new', 'UserController@storeUser');
        Route::get('/{user_type}/{user_id}/edit', 'UserController@editUser');
        Route::get('/{user_type}/{user_id}/edit/step/{step?}', 'UserController@editUser');
    });
});


