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
            // run
            $results = \Twitter::getSearch($input);

            // return
            return $results;
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
        $feed->description = $feed->title;
        $feed->link = static::filter(\URL::current().'?'.http_build_query(\Input::all()));
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
                $link = static::filter('https://twitter.com/'.$handle.'/status/'.ex($status, 'id'));
                $date = ex($status, 'created_at');

                // add to feed
                $feed->add(null, null, $link, $date, null); // title, author, link, date, description
            }
        }

        // return
        return $feed->render('atom');
    }

    /**
     * Return a filtered URL string.
     *
     * @param   string  $string
     * @return  string
     */
    protected static function filter($string)
    {
        // make filter
        $filters = array();
        $filters['&'] = '&amp;';

        // search and replace
        $find = array_keys($filters);
        $replace = array_values($filters);

        // run
        return str_ireplace($find, $replace, $string);
    }

}