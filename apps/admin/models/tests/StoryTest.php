<?php
namespace yiiunit\framework\base;
use Yii;
use app\models\Story;

/**
 * @group base
 */
class StoryTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }
    public function testGetAttributes()
    {
        $story = new Story;
        $story->story_id = '23';
        $story->title = 'Test Story';
        $story->link = 'http://www.google.com';
        $story->description = 'this is a test story.';
        $story->story_date = '9/22/1984';
        $story->story_size = 'MEDIUM';
        $story->show_desc = true;
        $story->visible = 1;
        $expected = 
        $this->assertEquals(array(
            'title' => 'Test Story',
            'description' => 'this is a test story.'
        ), $story->getAttributes());
        $this->assertEquals(array(
            'title' => 'Test Story',
            'description' => 'this is a test story.'
        ), $story->getAttributes(array('title', 'description')));
        $this->assertEquals(array(
            'title' => 'Test Story',
            'description' => 'this is a test story.'
        ), $story->getAttributes(null, array('customLabel', 'underscore_style')));
        $this->assertEquals(array(
            'title' => 'Test Story'
        ), $story->getAttributes(array('title', 'description'), array('description', 'customLabel', 'underscore_style')));
    }
}