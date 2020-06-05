<?php


namespace Further\Mailmatch\Drivers;


interface DriverInterface
{
    public function register();

    public function store($event);
}
