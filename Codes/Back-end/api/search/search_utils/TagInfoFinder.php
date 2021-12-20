<?php


class TagInfoFinder
{
    private $course_ids, $instructor_ids, $semesters, $major_ids;
    private $database;
    function __construct($tags, $database)
    {
        $this->course_ids = [];
        $this->instructor_ids = [];
        $this->semesters = [];
        $this->major_ids = [];
        $this->database = $database;
        $n = count($tags);
        for ($i=0 ; $i<$n ; $i++) {
            $firstChar = substr($tags[$i],0,1);
            $firstChar = strtoupper($firstChar);
            $content = [substr($tags[$i],1)];
            switch ($firstChar) {
                case "I":
                    $this->instructor_ids = array_merge($this->instructor_ids,$content);
                    break;
                case "M":
                    $this->major_ids = array_merge($this->major_ids,$content);
                    break;
                case "C":
                    $this->course_ids = array_merge($this->course_ids,$content);
                    break;
                case "S":
                    $this->semesters = array_merge($this->semesters,[$tags[$i]]);
                    break;
            }
        }
    }

    public function get_courses_titles()
    {
        global $TITLE_KEY_L, $COURSE_KEY_L, $COURSE_ID_KEY_L_S;
        $n = count($this->course_ids);
        $titles = [];
        if ($n>0) {
            $query = "SELECT $TITLE_KEY_L FROM $COURSE_KEY_L WHERE";
            for ($i = 0 ; $i<$n ; $i++) {
                $input_id = $this->course_ids[$i];
                $query.= " $COURSE_ID_KEY_L_S = $input_id";
                if ($i == $n-1)
                    $query.=";";
                else $query.=" OR";
            }
            $title_relation = $this->database->connection->query($query);
            $index = 0;
            while ($title_tuple = $title_relation->fetch_assoc()) {
                $titles[$index] = $title_tuple[$TITLE_KEY_L];
                $index++;
            }
        }
        return $titles;
    }

    public function get_semesters() {
        return $this->semesters;
    }

    public function get_major_titles()
    {
        global $TITLE_KEY_L, $MAJOR_KEY_L, $MAJOR_ID_KEY_L_S;
        $n = count($this->major_ids);
        $titles = [];
        if ($n>0) {
            $query = "SELECT $TITLE_KEY_L FROM $MAJOR_KEY_L WHERE";
            for ($i = 0 ; $i<$n ; $i++) {
                $input_id = $this->major_ids[$i];
                $query.= " $MAJOR_ID_KEY_L_S = $input_id";
                if ($i == $n-1)
                    $query.=";";
                else $query.=" OR";
            }
            $title_relation = $this->database->connection->query($query);
            $index = 0;
            while ($title_tuple = $title_relation->fetch_assoc()) {
                $titles[$index] = $title_tuple[$TITLE_KEY_L];
                $index++;
            }
        }
        return $titles;
    }

    public function get_instructor_names()
    {
        global $NAME_KEY_L, $INSTRUCTOR_KEY_L, $INSTRUCTOR_ID_KEY_L_S;
        $n = count($this->instructor_ids);
        $names = [];
        if ($n>0) {
            $query = "SELECT $NAME_KEY_L FROM $INSTRUCTOR_KEY_L WHERE";
            for ($i = 0 ; $i<$n ; $i++) {
                $input_id = $this->instructor_ids[$i];
                $query.= " $INSTRUCTOR_ID_KEY_L_S = $input_id";
                if ($i == $n-1)
                    $query.=";";
                else $query.=" OR";
            }
            $names_relation = $this->database->connection->query($query);
            $index = 0;
            while ($title_tuple = $names_relation->fetch_assoc()) {
                $names[$index] = $title_tuple[$NAME_KEY_L];
                $index++;
            }
        }
        return $names;
    }
}