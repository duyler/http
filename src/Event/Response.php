<?php

namespace Duyler\Http\Event;

enum Response
{
    case ResponseCreated;
    case ResponseHasBeenSent;
}
