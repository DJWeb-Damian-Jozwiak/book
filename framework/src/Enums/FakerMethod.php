<?php

namespace DJWeb\Framework\Enums;

enum FakerMethod: string
{
    case NAME = 'name';
    case EMAIL = 'safeEmail';
    case PHONE = 'phoneNumber';
    case DATE = 'date';
    case TIME = 'time';
    case ADDRESS = 'address';
    case COMPANY = 'company';
    case CITY = 'city';
    case STATE = 'state';
    case COUNTRY = 'country';
    case PASSWORD = 'password';
}