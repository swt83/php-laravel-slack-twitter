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
        $hash = 'slack_twitter_'.md5(serialize($input));

        // load from cache...
        $results = \Cache::remember($hash, 5, function() use($hash, $input)
        {
            // run
            return \Twitter::getSearch($input);
        });

        // name
        $name = ex($input, 'name', $hash);

        // return
        return static::to_rss($name, $results);
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
        $feed->pubdate = time();
        $feed->lang = 'en';

        // foreach result...
        foreach (ex($results, 'statuses', array()) as $status)
        {
            // vars
            $link = 'https://twitter.com/_/status/'.ex($status, 'id');
            $date = ex($status, 'created_at');

            // add to feed
            $feed->add(null, null, $link, $date, null); // title, author, link, date, description
        }

        // return
        return $feed->render('atom');
    }

}