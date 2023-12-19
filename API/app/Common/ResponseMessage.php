<?php

namespace App\Common;

class ResponseMessage
{
    const OK = 'Successfully';
    const CREATE_SUCCESS = 'Successfully created';
    const UPDATE_SUCCESS = 'Successfully updated';
    const DELETE_SUCCESS = 'Successfully deleted';
    const NOT_FOUND_ERROR = 'Not found';
    const AUTH_ERROR = 'Authentication error';
    const AUTHORIZATION_ERROR = 'Authorization error';
    const VALIDATION_ERROR = 'Validation error';
}
