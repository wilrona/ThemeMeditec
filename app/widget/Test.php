<?php


use TypeRocket\Register\BaseWidget;

class Test_Widget extends BaseWidget
{

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        parent::__construct( 'Test_Widget', 'Widget Test', [
            'classname' => 'Test_Widget',
            'description' => 'Test Widget is awesome'
        ] );
    }

    public function backend($fields)
    {
        // TODO: Implement backend() method.
        echo $this->form->text('Title');
        echo $this->form->date('Date');
    }

    public function frontend($args, $fields)
    {
        // TODO: Implement frontend() method.
    }

    public function save($new_fields, $old_fields)
    {
        // TODO: Implement save() method.
        return $new_fields;
    }
}