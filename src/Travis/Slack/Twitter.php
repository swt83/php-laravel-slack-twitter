<?php

namespace Travis\Slack;

class Twitter {

    /**
     * Handle incoming search request.
     *
     * @return  string
     */
    public static function search()
    {
        // capture
        $input = \Input::all();

        // catch error...
        if (!$input) trigger_error('No input.');

        // hash
        $hash = md5(serialize($input));

        // load from cache...
        $results = \Cache::remember('slack_twitter_'.$hash, 5, function() use($hash, $input)
        {
            // fetch
            $tweets = \Twitter::getSearch($input);

            // name
            $name = \Input::get('name', $hash);

            // build
            $rss = \Travis\Slack\Twitter::to_rss($name, $tweets);

            // return
            return $rss->getOriginalContent()->render();
        });

        // return
        return \Response::make($results)->header('Content-Type', 'application/rss+xml');
    }

    /**
     * Return an RSS feed w/ a given name.
     *
     * @param   string  $name
     * @param   mixed   $results
     * @return  string
     */
    protected static function to_rss($name, $results)
    {
        // new feed
        $feed = \Feed::make();

        // set title
        $feed->title = 'twitter ['.strtolower($name).']';
        $feed->description = $feed->title;
        $feed->link = \URL::current().'?'.http_build_query(\Input::all(), '', '&amp;');
        $feed->lang = 'en';

        // get statuses
        $statuses = ex($results, 'statuses', array());

        // if results...
        if ($statuses)
        {
            // foreach result...
            foreach ($statuses as $status)
            {
                // vars
                $handle = strtolower(ex($status, 'user.screen_name', '_'));
                $link = 'https://twitter.com/'.$handle.'/status/'.ex($status, 'id');
                $date = ex($status, 'created_at');
                $description = ex($status, 'text');

                // add to feed
                $feed->add($handle, null, $link, $date, $description); // title, author, link, date, description
            }
        }

        // return
        return $feed->render('atom');
    }

}