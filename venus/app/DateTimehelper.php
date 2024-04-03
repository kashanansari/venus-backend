<?php
use Illuminate\Support\Carbon;

    function formatDate()
    {
        
        $date=Carbon::now()->setTimezone('Asia/karachi')->format('d-m-Y');
        // $time=Carbon::now()->setTimezone('Asia/karachi')->format('h:i A');
        return $date;
    }
    function formatTime()
    {
        
        // $time=Carbon::now()->setTimezone('Asia/karachi')->format('d-m-Y');
        $time=Carbon::now()->setTimezone('Asia/karachi')->format('h:i A');
        return $time;
        
    }
