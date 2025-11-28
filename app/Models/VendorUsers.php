<?php
/**
 * File name: VendorUser.php
 * Last modified: 2020.06.11 at 16:10:52
 * Copyright (c) 2020
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 * @version July 10, 2018, 11:44 am UTC
 *
 * @property \App\Models\Cart[] cart
 * @property string name
 * @property string email
 * @property string password
 * @property string api_token
 * @property string device_token
 */
class VendorUsers extends Authenticatable
{
    

    /**
     * Validation rules
     *
     * @var array
     */

    public $table = 'vendor_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

}
