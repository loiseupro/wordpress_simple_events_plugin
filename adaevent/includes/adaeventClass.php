<?php

//require_once explode("wp-content", __FILE__)[0] . "wp-load.php";

class adaevent {

    /**
     * Get all events
     *
     * @return array
     */
    public function getEvents() {
        global $wpdb;
        $sql = 'SELECT * FROM `' . $wpdb->prefix . ADAEVENT_TABLE_NAME . '` ;';
        return $wpdb->get_results($sql);
    }

    /**
     * Get event by id
     * 
     * @param string $id  
     *
     * @return array
     */
    public function getEventById($id) {
        global $wpdb;
        $sql = 'SELECT * FROM `' . $wpdb->prefix . ADAEVENT_TABLE_NAME . '` WHERE id = ' . $id;
        return (array)$wpdb->get_row($sql);
    }

    /**
     * Check if event exists
     *
     * @param string $id  
     * 
     * @return bool
     */
    public function existsEvent($id) {
        global $wpdb;
        $sql = 'SELECT * FROM `' . $wpdb->prefix . ADAEVENT_TABLE_NAME . '` WHERE id = ' . $id;
        $result = $wpdb->get_results($sql);
        if (count($result) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Save or update event
     *
     * @param array $event 
     * 
     * @return int | bool
     */
    public function setEvent($event) {
        global $wpdb;

        if (!is_array($event) or !isset($event["name"])) {
            return false;
        }

        $result = false;
        $name = $event["name"];
        $place = "";
        $start = "";
        $end = "";
        $user = get_current_user_id();

        if (isset($event["place"])) $place = $event["place"];
        if (isset($event["start"])) $start = $event["start"];
        if (isset($event["end"])) $end = $event["end"];

        if (isset($event["id"]) and $this->existsEvent($event["id"])) {
            // Update
            $result = $wpdb->update(
                $result = $wpdb->prefix . ADAEVENT_TABLE_NAME,
                array(
                    'name' => $name,
                    'place' => $place,
                    'start' => $start,
                    'end' => $end,
                    'user_update' => $user
                ),
                array(
                    'id' => $event["id"]
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d'
                ),
                array('%d')
            );
        } else {
            // Create
            $result = $wpdb->insert(
                $wpdb->prefix . ADAEVENT_TABLE_NAME,
                array(
                    'name' => $name,
                    'place' => $place,
                    'start' => $start,
                    'end' => $end,
                    'user_update' => $user,
                    'user_add' => $user
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d'
                )
            );
        }

        if (!$result) return false;

        if (!isset($event["id"]) or !$this->existsEvent($event["id"])) {
            global $wpdb;
            $sql = 'SELECT max(id) as id FROM `' . $wpdb->prefix . ADAEVENT_TABLE_NAME . '`';
            $res = (array)$wpdb->get_row($sql);
            if (isset($res["id"])) return $res["id"];
        }
        return $event["id"];
    }


    /**
     * Delete event
     *
     * @param string $id  
     * 
     * @return bool
     */
    public function deleteEvent($id) {
        global $wpdb;
        return $wpdb->delete(
            $wpdb->prefix . ADAEVENT_TABLE_NAME,
            array('id' => $id),
            array(
                '%d'
            )
        );
    }


    /**
     * Build events list in html
     *
     * @param array $events  
     * @param bool $only_future  
     * 
     * @return bool
     */
    public function buildEvenListHtml($events, $only_future) {
   
        if (count($events) == 0) return '';

        if ($only_future == 'true') {
            $only_future = true;
        } else {
            $only_future = false;
        }

        $html = '<div class="aevents-list">';

        foreach ($events as $event) {
            $event = (object)$event;
            $add = true;
            
            if ($only_future and str_replace('-', '',$event->start) > date('Ymd')) {
                $add = false;
            }

            if ($add) {
                $html .= '<div class="aevents-item">';
                $html .= '<p class="aevents-event-title">' . $event->name . '</p>';
                $html .= '<p class="aevents-event-place">' . $event->place . '</p>';
                $html .= '<p class="aevents-event-dates">';
                $html .=  __("From", 'adaevent');
                $html .= ' ' . $event->start . ' ';
                $html .=  __("to", 'adaevent');
                $html .= ' ' . $event->end . ' ';
                $html .= '</p>';
                $html .= '</div>';
            }
        }

        $html .= '</div>';

        return $html;
    }
}
