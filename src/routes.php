<?php

Route::get('slack/twitter/search', function()
{
    return Travis\Slack\Twitter::search();
});