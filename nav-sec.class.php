

class NavSec
{
    private $current_post = 0;
    private $top_ancestor = 0;
    private $depth = 2;
    private $output_lines = array();
    private $current_class = 'current';
    private $nav_id = 'sideNav';
    
    public function __construct( $current_post )
    {
	$this->current_post = $current_post;
	$this->top_ancestor = $this->get_top_level_ancestor( $this->current_post->ID );
	
	
    }
    
    
    /**
     * Find the page that is the highest ancestor of the current page. Normally a section page from the primary Nav
     * @param int $post_id
     * @return Object
     */
    function get_top_level_ancestor( $post_id )
    {
	$tmp = get_page( $id );
	while ( intVal( $tmp->post_parent ) > 0 ) {
	    $tmp = get_page( $tmp->post_parent );
	}
	return $tmp;
    }
    
    /**
     * Get 1 level of child pages for the supplied post ID
     * @param int $post_id
     * @return array 
     */
    function get_child_pages( $post_id )
    {
	return get_pages( array( 
	    'child_of' => $post_id, 
	    'parent' => $post_id, 
	    'sort_order' => 'ASC', 
	    'sort_column' => 'menu_order',
	    'hierarchical'  => 0
	) );
    }
    
    /**
     * Output the HTML of the lists
     */
    function render()
    {
	// Find top section page
	$section_post = get_post( $this->top_ancestor );
		
	// Loop thru secondary level pages
	$this->level( $this->top_ancestor->ID );

	// output the lines that have been made
	echo implode( "\r\n", $this->output_lines );
    }
    
    /**
     * Check if current nav item in the loop is the current page, or an ancestor of it.
     * @param Int $post_id
     * @return bool 
     */
    function is_current( $post_id )
    {
	return ( in_array( $post_id, $this->current_post->ancestors ) || $this->current_post->ID === $post_id );
    }
        
    /**
     * Loop thru current level of pages. For the current item, check if there are sub pages, and call this function again inside to do that level
     * @param Object $page
     * @return bool 
     */
    function level( $page )
    {
	// Get all second level pages in this section
	$pages = $this->get_child_pages( $page );
	
	// Check if there are any sub pages
	$count_pages = count($pages);
	if( !$count_pages ){
	    return false;
	}
	
	$this->output_lines[] = '<ul id="' . $this->get_ul_id( $this->depth ) . '">';
	
	// Loop pages and make menu items
	$i = 1;
	foreach( $pages as $item ){
	    $class = '';
	    if( $count_pages == $i ){
		$class .= 'last'  ;
	    }
	    if ( $this->is_current( $item->ID ) ) {
		$class .= ' ' . $this->current_class;
		// Current second level page
		$this->output_lines[] = sprintf( 
		    '<li class="%s"><a href="%s">%s</a>', 
			$class, 
			get_permalink( $item->ID ), 
			$item->post_title 
		);
		// Check for Sub Pages, and display sub list
		$this->level( $item->ID );
		

		$this->output_lines[] = '</li>';
	    }else{
		// normal list items, not current, no subnav
		$this->output_lines[] = sprintf( '<li class="%s"><a href="%s">%s</a></li>', $class, get_permalink( $item->ID ), $item->post_title );
	    }
	    $i ++;
	}
	
	$this->output_lines[] = '</ul>';
	
	$this->depth ++;
	return true;
    }
    
    function get_ul_id( $level )
    {
	switch( $level ){
	    case 2:
		return $this->nav_id;
		break;
	    case 3:
		return 'subNav';
		break;
	    default:
		return 'subNav_' . $level;
		break;
	}
    }
    
}