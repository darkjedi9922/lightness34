<?php /** @var frame\views\Layout $self */
use frame\auth\InitAccess;
InitAccess::accessRight('stat', 'see');
$self->loadChild()->show();