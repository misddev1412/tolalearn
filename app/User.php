<?php

namespace App;

use App\Models\Accounting;
use App\Models\Badge;
use App\Models\Meeting;
use App\Models\Noticeboard;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\QuizzesResult;
use App\Models\Role;
use App\Models\Follow;
use App\Models\Sale;
use App\Models\Section;
use App\Models\Webinar;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    static $active = 'active';
    static $pending = 'pending';
    static $inactive = 'inactive';

    protected $dateFormat = 'U';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $hidden = [
        'password', 'remember_token', 'google_id', 'facebook_id', 'role_id'
    ];

    static $statuses = [
        'active', 'pending', 'inactive'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    private $permissions;
    private $user_group;
    private $userInfo;


    static function getAdmin()
    {
        $role = Role::where('name', Role::$admin)->first();

        $admin = self::where('role_name', $role->name)
            ->where('role_id', $role->id)
            ->first();

        return $admin;
    }

    public function isAdmin()
    {
        return $this->role->is_admin;
    }

    public function isUser()
    {
        return $this->role_name === Role::$user;
    }

    public function isTeacher()
    {
        return $this->role_name === Role::$teacher;
    }

    public function isOrganization()
    {
        return $this->role_name === Role::$organization;
    }

    public function hasPermission($section_name)
    {
        if (self::isAdmin()) {
            if (!isset($this->permissions)) {
                $sections_id = Permission::where('role_id', '=', $this->role_id)->where('allow', true)->pluck('section_id')->toArray();
                $this->permissions = Section::whereIn('id', $sections_id)->pluck('name')->toArray();
            }
            return in_array($section_name, $this->permissions);
        }
        return false;
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }

    public function getAvatar()
    {
        if (!empty($this->avatar)) {
            $imgUrl = '/store/' . $this->id . '/' . $this->avatar;
        } else {
            $imgUrl = getPageBackgroundSettings('user_avatar');
        }

        return $imgUrl;
    }

    public function getCover()
    {
        if (!empty($this->cover_img)) {
            $path = str_replace('/storage', '', $this->cover_img);

            $imgUrl = url($path);
        } else {
            $imgUrl = getPageBackgroundSettings('user_cover');
        }

        return $imgUrl;
    }

    public function getProfileUrl()
    {
        return '/users/' . $this->id . '/profile';
    }

    public function getUserGroup()
    {
        if (empty($this->user_group)) {
            if (!empty($this->userGroup) and !empty($this->userGroup->group) and $this->userGroup->group->status == 'active') {
                $this->user_group = $this->userGroup->group;
            }
        }

        return $this->user_group;
    }


    public static function generatePassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function meeting()
    {
        return $this->hasOne('App\Models\Meeting', 'creator_id', 'id');
    }

    public function hasMeeting()
    {
        return Meeting::where('disabled', false)
            ->where('creator_id', $this->id)
            ->first();
    }

    public function ReserveMeetings()
    {
        return $this->hasMany('App\Models\ReserveMeeting', 'user_id', 'id');
    }

    public function followers()
    {
        return Follow::where('user_id', $this->id)->where('status', Follow::$accepted)->get();
    }

    public function following()
    {
        return Follow::where('follower', $this->id)->where('status', Follow::$accepted)->get();
    }

    public function webinars()
    {
        return $this->hasMany('App\Models\Webinar', 'creator_id', 'id')
            ->orWhere('teacher_id', $this->id);
    }

    public function getActiveWebinars($just_count = false)
    {
        $webinars = Webinar::where('status', 'active')
            ->where(function ($query) {
                $query->where('creator_id', $this->id)
                    ->orWhere('teacher_id', $this->id);
            })
            ->orderBy('created_at', 'desc');

        if ($just_count) {
            return $webinars->count();
        }

        return $webinars->get();
    }

    public function userMetas()
    {
        return $this->hasMany('App\Models\UserMeta');
    }

    public function carts()
    {
        return $this->hasMany('App\Models\Cart', 'creator_id', 'id');
    }

    public function userGroup()
    {
        return $this->belongsTo('App\Models\GroupUser', 'id', 'user_id');
    }

    public function certificates()
    {
        return $this->hasMany('App\Models\Certificate', 'student_id', 'id');
    }

    public function customBadges()
    {
        return $this->hasMany('App\Models\UserBadge', 'user_id', 'id');
    }

    public function supports()
    {
        return $this->hasMany('App\Models\Support', 'user_id', 'id');
    }

    public function occupations()
    {
        return $this->hasMany('App\Models\UserOccupation', 'user_id', 'id');
    }

    public function organization()
    {
        return $this->hasOne($this, 'id', 'organ_id');
    }


    public function getOrganizationTeachers()
    {
        return $this->hasMany($this, 'organ_id', 'id')->where('role_name', Role::$teacher);
    }

    public function getOrganizationStudents()
    {
        return $this->hasMany($this, 'organ_id', 'id')->where('role_name', Role::$user);
    }

    public function zoomApi()
    {
        return $this->hasOne('App\Models\UserZoomApi', 'user_id', 'id');
    }


    public function rates()
    {
        $webinars = $this->webinars()
            ->where('status','active')
            ->get();

        $rate = 0;

        if (!empty($webinars)) {
            $rates = 0;
            $count = 0;

            foreach ($webinars as $webinar) {
                $webinarRate = $webinar->getRate();

                if (!empty($webinarRate) and $webinarRate > 0) {
                    $count += 1;
                    $rates += $webinarRate;
                }
            }

            if ($rates > 0) {
                if ($count < 1) {
                    $count = 1;
                }

                $rate = number_format($rates / $count, 2);
            }
        }

        return $rate;
    }

    public function reviewsCount()
    {
        $webinars = $this->webinars;
        $count = 0;

        if (!empty($webinars)) {
            foreach ($webinars as $webinar) {
                $count += $webinar->reviews->count();
            }
        }

        return $count;
    }

    public function getBadges($customs = true, $getNext = false)
    {
        return Badge::getUserBadges($this, $customs, $getNext);
    }

    public function getCommission()
    {
        $commission = 0;
        $financialSettings = getFinancialSettings();

        if (!empty($financialSettings) and !empty($financialSettings['commission'])) {
            $commission = (int)$financialSettings['commission'];
        }

        $getUserGroup = $this->getUserGroup();
        if (!empty($getUserGroup) and isset($getUserGroup->commission)) {
            $commission = $getUserGroup->commission;
        }

        if (!empty($this->commission)) {
            $commission = $this->commission;
        }

        return $commission;
    }

    public function getIncome()
    {
        $totalIncome = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$income)
            ->where('type', Accounting::$addiction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        return $totalIncome;
    }

    public function getPayout()
    {
        $credit = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$income)
            ->where('type', Accounting::$addiction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        $debit = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$income)
            ->where('type', Accounting::$deduction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        return $credit - $debit;
    }

    public function getAccountingCharge()
    {
        $query = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$asset)
            ->where('system', false)
            ->where('tax', false);

        $additions = deepClone($query)->where('type', Accounting::$addiction)
            ->sum('amount');

        $deductions = deepClone($query)->where('type', Accounting::$deduction)
            ->sum('amount');

        $charge = $additions - $deductions;
        return $charge > 0 ? $charge : 0;
    }

    public function getAccountingBalance()
    {
        $additions = Accounting::where('user_id', $this->id)
            ->where('type', Accounting::$addiction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        $deductions = Accounting::where('user_id', $this->id)
            ->where('type', Accounting::$deduction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        $balance = $additions - $deductions;
        return $balance > 0 ? $balance : 0;
    }

    public function getPurchaseAmounts()
    {
        return Sale::where('buyer_id', $this->id)
            ->sum('amount');
    }

    public function getSaleAmounts()
    {
        return Sale::where('seller_id', $this->id)
            ->sum('amount');
    }

    public function sales()
    {
        $webinarIds = Webinar::where('creator_id', $this->id)->pluck('id')->toArray();

        return Sale::whereIn('webinar_id', $webinarIds)->sum('amount');
    }

    public function salesCount()
    {
        $webinarIds = $this->webinars()->pluck('id')->toArray();

        return Sale::whereIn('webinar_id', $webinarIds)->count();
    }

    public function getUnReadNotifications()
    {
        $notifications = Notification::where(function ($query) {
            $query->where(function ($query) {
                $query->where('user_id', $this->id)
                    ->where('type', 'single');
            })->orWhere(function ($query) {
                if (!$this->isAdmin()) {
                    $query->whereNull('user_id')
                        ->whereNull('group_id')
                        ->where('type', 'all_users');
                }
            });
        })->doesntHave('notificationStatus')
            ->orderBy('created_at', 'desc')
            ->get();

        $userGroup = $this->userGroup()->first();
        if (!empty($userGroup)) {
            $groupNotifications = Notification::where('group_id', $userGroup->group_id)
                ->where('type', 'group')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();

            if (!empty($groupNotifications) and !$groupNotifications->isEmpty()) {
                $notifications = $notifications->merge($groupNotifications);
            }
        }

        if ($this->isUser()) {
            $studentsNotifications = Notification::whereNull('user_id')
                ->whereNull('group_id')
                ->where('type', 'students')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();
            if (!empty($studentsNotifications) and !$studentsNotifications->isEmpty()) {
                $notifications = $notifications->merge($studentsNotifications);
            }
        }

        if ($this->isTeacher()) {
            $instructorNotifications = Notification::whereNull('user_id')
                ->whereNull('group_id')
                ->where('type', 'instructors')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();
            if (!empty($instructorNotifications) and !$instructorNotifications->isEmpty()) {
                $notifications = $notifications->merge($instructorNotifications);
            }
        }

        if ($this->isOrganization()) {
            $organNotifications = Notification::whereNull('user_id')
                ->whereNull('group_id')
                ->where('type', 'organizations')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();
            if (!empty($organNotifications) and !$organNotifications->isEmpty()) {
                $notifications = $notifications->merge($organNotifications);
            }
        }

        return $notifications;
    }

    public function getUnreadNoticeboards()
    {
        $noticeboards = Noticeboard::where(function ($query) {
            $query->whereNotNull('organ_id')
                ->where('organ_id', $this->organ_id)
                ->where(function ($query) {
                    if ($this->isOrganization()) {
                        $query->where('type', 'organizations');
                    } else {
                        $type = 'students';

                        if ($this->isTeacher()) {
                            $type = 'instructors';
                        }

                        $query->whereIn('type', ['students_and_instructors', $type]);
                    }
                });
        })->orWhere(function ($query) {
            $type = ['all'];

            if ($this->isUser()) {
                $type = array_merge($type, ['students', 'students_and_instructors']);
            } elseif ($this->isTeacher()) {
                $type = array_merge($type, ['instructors', 'students_and_instructors']);
            } elseif ($this->isOrganization()) {
                $type = array_merge($type, ['organizations']);
            }

            $query->whereNull('organ_id')
                ->whereIn('type', $type);
        })->orderBy('created_at', 'desc')
            ->get();


        /*
        ->whereDoesntHave('noticeboardStatus', function ($qu) {
            $qu->where('user_id', $this->id);
        })
        */

        return $noticeboards;
    }

    public function getPurchasedCoursesIds()
    {
        $webinarIds = [];

        $sales = Sale::where('buyer_id', $this->id)
            ->whereNotNull('webinar_id')
            ->where('type', 'webinar')
            ->whereNull('refund_at')
            ->get();

        foreach ($sales as $sale) {
            if ($sale->payment_method == Sale::$subscribe) {
                $subscribe = $sale->getUsedSubscribe($sale->buyer_id, $sale->webinar_id);

                if (!empty($subscribe)) {
                    $subscribeSale = Sale::where('buyer_id', $this->id)
                        ->where('type', Sale::$subscribe)
                        ->where('subscribe_id', $subscribe->id)
                        ->whereNull('refund_at')
                        ->first();

                    if (!empty($subscribeSale)) {
                        $usedDays = (int)diffTimestampDay(time(), $subscribeSale->created_at);
                        if ($usedDays <= $subscribe->days) {
                            $webinarIds[] = $sale->webinar_id;
                        }
                    }
                }
            } else {
                $webinarIds[] = $sale->webinar_id;
            }
        }

        return $webinarIds;
    }

    public function getActiveQuizzesResults($group_by_quiz = false, $status = null)
    {
        $query = QuizzesResult::where('user_id', $this->id);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if ($group_by_quiz) {
            $query->groupBy('quiz_id');
        }

        return $query->get();
    }
}
