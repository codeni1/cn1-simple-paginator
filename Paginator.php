<?php
/**
 * Date: 4/27/2019
 * Time: 11:24 PM
 */

class Paginator
{
    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $page;

    /**
     * @var mixed
     */
    private $db;

    /**
     * @var string
     */
    private $query;

    /**
     * Paginator constructor.
     * @param $db
     * @param $query
     */
    function __construct($db, $query)
    {
        $this->db = $db;
        $this->query = strtolower($query);
    }

    /**
     * @param int $limit
     * @param int $page
     * @return mixed
     */
    function getData($limit = 30, $page = 1) {

        //if not a valid number then set to default 30
        $limit = (int)$limit > 0 ? $limit : 30;

        //if not a valid number then set to 1
        $page = (int)$page > 0 ? $page : 1;

        $this->limit = $limit;
        $this->page = $page;

        //get the offset
        $offset = ($page - 1) * $this->limit;

        //set the offset and limit
        $query = $this->query;
        $query .= sprintf(' limit %d, %d', $offset, $this->limit);

        //query results
        $results = $this->db->query($query);

        $data = [];
        while($row = $results->fetch_array(MYSQLI_ASSOC))
        {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @return string
     */
    function createNumberLinks()
    {
        $results = $this->db->query($this->query);
        $pages = ceil($results->num_rows / $this->limit);

        //return empty string is not data from the query
        if (count($results) <= $this->limit)
            return '';

        $prevLI = '';
        $nextLI = '';

        //this is for the Previous and Next link
        if ($pages > 1) {
            if ($this->page <= 1) {
                $prevLI = '<li style="list-style:none;display: inline;">&#171; Prev</li>';
            } else {
                $prev = $this->page - 1;
                $prevLI = '<li style="list-style:none;display: inline;"><a href="?page=' . $prev . '">&#171; Prev</a> </li>';
            }

            if ($this->page >= $pages) {
                $nextLI = '<li style="list-style:none;display: inline;">Next &#187;</li>';
            } else {
                $next = $this->page + 1;
                $nextLI = '<li style="list-style:none;display: inline;"> <a href="?page=' . $next . '">Next &#187;</a></li>';
            }
        }

        $links = '<ul class="pagination">';
        $links .= $prevLI;

        //current page selected
        $currentPage = $this->page;

        //no of pages you want to show
        //1 2 3 4 5 6 7 8 9 10 etc..
        $noToShow = 10;

        //half of number to show then round off the number
        $halfOfNoToShow = round($noToShow / 2);

        if ($pages > $noToShow) {
            //if the current page is less than or equal to the half of the number to show plus 1 then
            //set start = 1 and end = number to show which is 10 set above
            if ($currentPage <= $halfOfNoToShow + 1) {
                $start = 1;
                $end = $noToShow;
            } else {
                //set the start to the current page (minus) half of the number to show plus 1
                $start = ($currentPage - $halfOfNoToShow) + 1;

                //set the end to the current page (plus) the number to show (minus) half of the number to show plus 1
                $end = $currentPage + ($noToShow - ($halfOfNoToShow + 1));

                //override the start an end
                //if the result of end is greater of equal to the total pages
                if ($end >= $pages) {

                    //then set the start to the pages (minus) the number to show then (plus) 1
                    $start = ($pages - $noToShow) + 1;

                    //set end equal to pages, in this example total pages is (equal) to 20 (minus) 1
                    //(minus) 1 so end will always be number of pages minus 1
                    //so if 20 then always 19 here would be the last page
                    //because the last 20 is populated below after the for loop
                    $end = $pages - 1;
                }
            }
        }  else {
            $start = 1;
            $end = $pages;
        }


        if ($pages > $noToShow) {
            //adding the ellipses and first page number 1
            if ($currentPage >= ($noToShow - $start)) {
                $links .= '<li style="list-style:none;display: inline;"><a href="?page=1">1</a></li>';
                $links .= '<li style="list-style:none;display: inline;">...</li>';
            }
        }

        for ($no = $start; $no <= $end; $no++) {
            $noLabel = $currentPage == $no ? '<b>[' . $no . ']</b>' : $no;
            $links .= '<li style="list-style:none;display: inline;"><a href="?page=' . $no . '">'.$noLabel.'</a></li>';
        }


        if ($pages > $noToShow) {
            //adding the ellipses and last page number
            if ($end < $pages - 1) {
                $links .= '<li style="list-style:none;display: inline;">...</li>';
                $links .= '<li style="list-style:none;display: inline;"><a href="?page=' . $pages . '">' . $pages . '</a></li>';
            } else {

                //append the last number
                $noLabel = $currentPage == $pages ? '<b>[' . $pages . ']</b>' : $pages;
                $links .= '<li style="list-style:none;display: inline;"><a href="?page=' . $pages . '">' . $noLabel . '</a></li>';
            }
        }

        $links .= $nextLI;
        $links .= '</ul>';

        //return the string with pagination
        return $links;
    }

}