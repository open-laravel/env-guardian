<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('services are in Services namespace')
    ->expect('OpenLaravel\EnvGuardian\Services')
    ->toBeClasses()
    ->not->toBeAbstract();

arch('commands extend Illuminate Console Command')
    ->expect('OpenLaravel\EnvGuardian\Commands')
    ->toExtend('Illuminate\Console\Command');

arch('commands are not used in services')
    ->expect('OpenLaravel\EnvGuardian\Services')
    ->not->toUse('OpenLaravel\EnvGuardian\Commands');

arch('services do not use env() helper')
    ->expect('OpenLaravel\EnvGuardian\Services')
    ->not->toUse('env');
