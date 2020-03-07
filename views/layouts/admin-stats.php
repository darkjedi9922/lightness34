<?php /** @var frame\views\Layout $self */
use frame\tools\Init;
Init::accessRight('stat', 'see');
$self->showChild();