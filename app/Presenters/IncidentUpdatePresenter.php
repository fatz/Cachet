<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Presenters;

use CachetHQ\Cachet\Dates\DateFactory;
use CachetHQ\Cachet\Presenters\Traits\TimestampsTrait;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Contracts\Support\Arrayable;
use McCool\LaravelAutoPresenter\BasePresenter;

/**
 * This is the incident update presenter.
 *
 * @author James Brooks <james@alt-three.com>
 */
class IncidentUpdatePresenter extends BasePresenter implements Arrayable
{
    use TimestampsTrait;

    /**
     * Inciden icon lookup.
     *
     * @var array
     */
    protected $icons = [
        0 => 'icon ion-android-calendar', // Scheduled
        1 => 'icon ion-alert-circled', // Investigating
        2 => 'icon ion-bug', // Identified
        3 => 'icon ion-eye', // Watching
        4 => 'icon ion-checkmark-circled greens', // Fixed
    ];

    /**
     * Renders the message from Markdown into HTML.
     *
     * @return string
     */
    public function formatted_message()
    {
        return Markdown::convertToHtml($this->wrappedObject->message);
    }

    /**
     * Return the raw text of the message, even without Markdown.
     *
     * @return string
     */
    public function raw_message()
    {
        return strip_tags($this->formatted_message());
    }

    /**
     * Present diff for humans date time.
     *
     * @return string
     */
    public function created_at_diff()
    {
        return app(DateFactory::class)->make($this->wrappedObject->created_at)->diffForHumans();
    }

    /**
     * Present formatted date time.
     *
     * @return string
     */
    public function created_at_formatted()
    {
        return ucfirst(app(DateFactory::class)->make($this->wrappedObject->created_at)->format($this->incidentDateFormat()));
    }

    /**
     * Formats the created_at time ready to be used by bootstrap-datetimepicker.
     *
     * @return string
     */
    public function created_at_datetimepicker()
    {
        return app(DateFactory::class)->make($this->wrappedObject->created_at)->format('d/m/Y H:i');
    }

    /**
     * Present formatted date time.
     *
     * @return string
     */
    public function created_at_iso()
    {
        return app(DateFactory::class)->make($this->wrappedObject->created_at)->toISO8601String();
    }

    /**
     * Returns a formatted timestamp for use within the timeline.
     *
     * @return string
     */
    public function timestamp_formatted()
    {
        if ($this->wrappedObject->is_scheduled) {
            return $this->scheduled_at_formatted;
        }

        return $this->wrappedObject->created_at->diffForHumans();
    }

    /**
     * Return the iso timestamp for use within the timeline.
     *
     * @return string
     */
    public function timestamp_iso()
    {
        if ($this->wrappedObject->is_scheduled) {
            return $this->scheduled_at_iso;
        }

        return $this->created_at_iso;
    }

    /**
     * Present the status with an icon.
     *
     * @return string
     */
    public function icon()
    {
        if (isset($this->icons[$this->wrappedObject->status])) {
            return $this->icons[$this->wrappedObject->status];
        }
    }

    /**
     * Returns a human readable version of the status.
     *
     * @return string
     */
    public function human_status()
    {
        return trans('cachet.incidents.status.'.$this->wrappedObject->status);
    }

    /**
     * Generate a permalink to the incident update.
     *
     * @return string
     */
    public function permalink()
    {
        return cachet_route('incident', [$this->wrappedObject->incident]).'#update-'.$this->wrappedObject->id;
    }

    /**
     * Convert the presenter instance to an array.
     *
     * @return string[]
     */
    public function toArray()
    {
        return array_merge($this->wrappedObject->toArray(), [
            'human_status' => $this->human_status(),
            'permalink'    => $this->permalink(),
            'created_at'   => $this->created_at(),
            'updated_at'   => $this->updated_at(),
        ]);
    }
}
