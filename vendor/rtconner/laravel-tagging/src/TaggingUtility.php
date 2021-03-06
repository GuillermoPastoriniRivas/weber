<?php

namespace Conner\Tagging;

use Illuminate\Support\Str;

/**
 * Utility functions to help with various tagging functionality.
 *
 * @author Rob Conner <rtconner@gmail.com>
 * @copyright Copyright (C) 2014 Robert Conner
 */
class TaggingUtility
{
    /**
     * Converts input into array
     *
     * @param string|array $tagNames
     * @return array
     */
    public static function makeTagArray($tagNames)
    {
        if(is_array($tagNames) && count($tagNames) == 1) {
            $tagNames = reset($tagNames);
        }

        if(is_string($tagNames)) {
            $tagNames = explode(',', $tagNames);
        } elseif(!is_array($tagNames)) {
            $tagNames = array(null);
        }

        $tagNames = array_map('trim', $tagNames);

        return array_values($tagNames);
    }

    public static function displayize($string)
    {
        $displayer = config('tagging.displayer');
        $displayer = empty($displayer) ? '\Illuminate\Support\Str::title' : $displayer;

        return call_user_func($displayer, $string);
    }

    public static function normalize($string)
    {
        $normalizer = config('tagging.normalizer');

        if(is_string($normalizer) && Str::contains($normalizer, 'Conner\Tagging\Util')) {
            $normalizer = '\Conner\Tagging\TaggingUtility::slug';
        }

        $normalizer = $normalizer ?: self::class.'::slug';

        return call_user_func($normalizer, $string);
    }

    /**
     * Create normalize string slug.
     *
     * Although supported, transliteration is discouraged because
     * 1) most web browsers support UTF-8 characters in URLs
     * 2) transliteration causes a loss of information
     *
     * @param string $str
     * @return string
     */
    public static function slug($str)
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8');

        $options = [
            'delimiter' => config('taggable.delimiter', '-'),
            'limit' => '255',
            'lowercase' => true,
            'replacements' => [],
            'transliterate' => true,
        ];

        $char_map = [
                // Latin
                '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'AE', '??' => 'C',
                '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I',
                '??' => 'D', '??' => 'N', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
                '??' => 'O', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'Y', '??' => 'TH',
                '??' => 'ss',
                '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'ae', '??' => 'c',
                '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i',
                '??' => 'd', '??' => 'n', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o',
                '??' => 'o', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'y', '??' => 'th',
                '??' => 'y',

                // Latin symbols
                '??' => '(c)',

                // Greek
                '??' => 'A', '??' => 'B', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Z', '??' => 'H', '??' => '8',
                '??' => 'I', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => '3', '??' => 'O', '??' => 'P',
                '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'Y', '??' => 'F', '??' => 'X', '??' => 'PS', '??' => 'W',
                '??' => 'A', '??' => 'E', '??' => 'I', '??' => 'O', '??' => 'Y', '??' => 'H', '??' => 'W', '??' => 'I',
                '??' => 'Y',
                '??' => 'a', '??' => 'b', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'z', '??' => 'h', '??' => '8',
                '??' => 'i', '??' => 'k', '??' => 'l', '??' => 'm', '??' => 'n', '??' => '3', '??' => 'o', '??' => 'p',
                '??' => 'r', '??' => 's', '??' => 't', '??' => 'y', '??' => 'f', '??' => 'x', '??' => 'ps', '??' => 'w',
                '??' => 'a', '??' => 'e', '??' => 'i', '??' => 'o', '??' => 'y', '??' => 'h', '??' => 'w', '??' => 's',
                '??' => 'i', '??' => 'y', '??' => 'y', '??' => 'i',

                // Turkish
                '??' => 'S', '??' => 'I', '??' => 'G',
                '??' => 's', '??' => 'i', '??' => 'g',

                // Russian
                '??' => 'A', '??' => 'B', '??' => 'V', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Yo', '??' => 'Zh',
                '??' => 'Z', '??' => 'I', '??' => 'J', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => 'O',
                '??' => 'P', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U', '??' => 'F', '??' => 'H', '??' => 'C',
                '??' => 'Ch', '??' => 'Sh', '??' => 'Sh', '??' => '', '??' => 'Y', '??' => '', '??' => 'E', '??' => 'Yu',
                '??' => 'Ya',
                '??' => 'a', '??' => 'b', '??' => 'v', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'yo', '??' => 'zh',
                '??' => 'z', '??' => 'i', '??' => 'j', '??' => 'k', '??' => 'l', '??' => 'm', '??' => 'n', '??' => 'o',
                '??' => 'p', '??' => 'r', '??' => 's', '??' => 't', '??' => 'u', '??' => 'f', '??' => 'h', '??' => 'c',
                '??' => 'ch', '??' => 'sh', '??' => 'sh', '??' => '', '??' => 'y', '??' => '', '??' => 'e', '??' => 'yu',
                '??' => 'ya',

                // Ukrainian
                '??' => 'Ye', '??' => 'I', '??' => 'Yi', '??' => 'G',
                '??' => 'ye', '??' => 'i', '??' => 'yi', '??' => 'g',

                // Czech
                '??' => 'C', '??' => 'D', '??' => 'E', '??' => 'N', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U',
                '??' => 'Z',
                '??' => 'c', '??' => 'd', '??' => 'e', '??' => 'n', '??' => 'r', '??' => 's', '??' => 't', '??' => 'u',
                '??' => 'z',

                // Polish
                '??' => 'A', '??' => 'C', '??' => 'e', '??' => 'L', '??' => 'N', '??' => 'S', '??' => 'Z',
                '??' => 'Z',
                '??' => 'a', '??' => 'c', '??' => 'e', '??' => 'l', '??' => 'n', '??' => 's', '??' => 'z',
                '??' => 'z',

                // Latvian
                '??' => 'A', '??' => 'E', '??' => 'G', '??' => 'i', '??' => 'k', '??' => 'L', '??' => 'N', '??' => 'u',
                '??' => 'a', '??' => 'e', '??' => 'g', '??' => 'i', '??' => 'k', '??' => 'l', '??' => 'n', '??' => 'u',

                //Romanian
                '??' => 'A', '??' => 'a', '??' => 'S', '??' => 's', '??' => 'T', '??' => 't',

                //Vietnamese
                '???' => 'a', '???' => 'A','???' => 'a', '???' => 'A', '???' => 'a', '???' => 'A', '???' => 'a', '???' => 'A',
                '???' => 'a', '???' => 'A', '???' => 'a', '???' => 'A', '???' => 'a', '???' => 'A', '???' => 'a', '???' => 'A',
                '???' => 'A', '???' => 'a', '???' => 'A', '???' => 'a', '??' => 'O', '??' => 'o', '??' => 'D', '??' => 'd',
                '???' => 'a', '???' => 'A', '???' => 'a', '???' => 'A', '???' => 'e', '???' => 'E', '???' => 'e', '???' => 'E',
                '???' => 'e', '???' => 'E', '???' => 'e', '???' => 'E', '???' => 'e', '???' => 'E',  '???' => 'e', '???' => 'E',
                '???' => 'e', '???' => 'E', '???' => 'e', '???' => 'E', '???' => 'i', '???' => 'I', '??' => 'i', '??' => 'I',
                '???' => 'i', '???' => 'I', '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O',
                '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O',
                '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O', '???' => 'o', '???' => 'O',
                '???' => 'o', '???' => 'O', '???' => 'u', '???' => 'U', '??' => 'u', '??' => 'U', '???' => 'u', '???' => 'U',
                '??' => 'u', '??' => 'U', '???' => 'u', '???' => 'U', '???' => 'u', '???' => 'U', '???' => 'u', '???' => 'U',
                '???' => 'u', '???' => 'U', '???' => 'u', '???' => 'U', '???' => 'y', '???' => 'Y', '???' => 'y', '???' => 'Y',
                '???' => 'y', '???' => 'Y', '???' => 'y', '???' => 'Y',
            
                //Kurdish
		'??' => 'a', '??' => 'b', '??' => 'c', '??' => 'd', '??' => 'e', '??' => 'f', '??' => 'g', '??' => 'j',
                '??' => 'k', '??' => 'l', '??' => 'm', '??' => 'n', '??' => 'o', '??' => 'p', '??' => 'q', '??' => 'r',
                '??' => 's', '??' => 't', '??' => 'v','????' => 'u', '??' => 'w', '??' => 'x', '??' => 'y', '??' => 'z',
                '??' => 'rr', '??' => 'e', '??' => 'hh', '??' => '', '??' => 'sh', '??' => 'gh', '??' => 'k', '??' => 'll',
                '??' => 'ch', '??' => 'h', "??" => '', '??' => 'e', '??' => 'h', '??' => 's', '??' => 'y', '??' => 'e',
                '??' => 't', '??' => 'z', '??' => 'u', '??' => 'dh', '??' => 'dh', '??' => 's', '??' => 'a', '??' => 'i',
                '??' => 'y', '??' => 'u',
        ];

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }
        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    /**
     * Private! Please do not call this function directly, just let the Tag library use it.
     * Increment count of tag by one. This function will create tag record if it does not exist.
     *
     * @param string $tagString
     * @param string $tagSlug
     * @param integer $count
     */
    public static function incrementCount($tagString, $tagSlug, $count)
    {
        if($count <= 0) { return; }
        $model = static::tagModelString();

        $tag = $model::where('slug', '=', $tagSlug)->first();

        if(!$tag) {
            $tag = new $model;
            $tag->name = $tagString;
            $tag->slug = $tagSlug;
            $tag->suggest = false;
            $tag->save();
        }

        $tag->count = $tag->count + $count;
        $tag->save();
    }

    /**
     * Private! Please do not call this function directly, let the Tag library use it.
     * Decrement count of tag by one. This function will create tag record if it does not exist.
     *
     * @param string $tagString
     */
    public static function decrementCount($tagString, $tagSlug, $count)
    {
        if($count <= 0) { return; }
        $model = static::tagModelString();

        $tag = $model::where('slug', '=', $tagSlug)->first();

        if($tag) {
            $tag->count = $tag->count - $count;
            if($tag->count < 0) {
                $tag->count = 0;
                \Log::warning("The '.$model.' count for `$tag->name` was a negative number. This probably means your data got corrupted. Please assess your code and report an issue if you find one.");
            }
            $tag->save();
        }
    }

    /**
     * Look at the tags table and delete any tags that are no longer in use by any taggable database rows.
     * Does not delete tags where 'suggest' is true
     *
     * @return int
     */
    public static function deleteUnusedTags()
    {
        $model = static::tagModelString();
        return $model::deleteUnused();
    }

    /**
     * @return string
     */
    public static function tagModelString()
    {
        return config('tagging.tag_model', '\Conner\Tagging\Model\Tag');
    }

    /**
     * @return string
     */
    public static function taggedModelString()
    {
        return config('tagging.tagged_model', '\Conner\Tagging\Model\Tagged');
    }

    /**
     * @return string
     */
    public static function tagGroupModelString()
    {
        return config('tagging.tag_group_model', '\Conner\Tagging\Model\TagGroup');
    }
}
