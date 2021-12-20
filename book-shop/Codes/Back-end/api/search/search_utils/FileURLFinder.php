<?php


class FileURLFinder
{

    public function get_urls($IDs,$database)
    {
        global $LINK_KEY_L, $FILE_KEY_L, $FILE_ID_KEY_L_S;
        $query = "SELECT $LINK_KEY_L FROM $FILE_KEY_L WHERE ";
        $n = count($IDs);
        if ($n==0) return [];
        for ($i = 0; $i < $n; $i++) {
            $query .= "$FILE_ID_KEY_L_S = $IDs[$i]";
            if ($i == $n - 1) $query.=";";
            else $query.=" OR ";
        }

        $urlsRelation = $database->connection->query($query);
        $urls = [];
        $index = 0;
        while ($urlTuple = $urlsRelation->fetch_assoc()) {
            $urls[$index] = $urlTuple[$LINK_KEY_L];
            $index++;
        }
        return $urls;
    }
}