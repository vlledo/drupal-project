<?php
namespace Drupal\Tests\mymodule\Functional;

use Drupal\Tests\BrowserTestBase;
/**
* Example functional test.
*
* @group mymodule
*/

class BrowserTest extends BrowserTestBase {

  protected $user;
  public static $modules = ['block', 'node', 'datetime'];

  protected function setUp() {
    parent::setUp();
    $this->drupalCreateContentType(['type' => 'page', 'name' => 'Basic page']);
    $this->user = $this->drupalCreateUser(['edit own page content', 'create page content']);
    $this->drupalPlaceBlock('local_tasks_block');
  }

  function testDrupalGet() {
    $this->drupalGet('user/register');
    $this->assertSession()->pageTextContains( 'Create new account');
    $this->assertSession()->fieldExists( 'Email address');
    $this->assertSession()->fieldExists( 'Username');
    $this->assertSession()->buttonExists( 'Create new account');
    $this->assertSession()->pageTextNotContains( 'Joomla');
  }

  function testNodeCreate() {
    $this->drupalLogin($this->user);
    $title = $this->randomString();
    $body = $this->randomString( 32);
    $edit = [
      'Title' => $title,
      'Body' => $body,
    ];
    $this->drupalPostForm( 'node/add/page', $edit, t('Save'));

    $node = $this->drupalGetNodeByTitle($title);
    $this->assertTrue($node);
    $this->assertEquals($title, $node->getTitle());
    $this->assertEquals($body, $node->body->value);

    $this->clickLink(t('Edit'));
    $this->assertSession()->addressEquals($node->toUrl('edit-form', ['absolute']));
    $link_text = 'Edit<span class="visually-hidden">(active tab)</span>' ;
    $this->assertSession()->responseContains($link_text);
    $this->assertSession()->fieldValueEquals('Title', $title);
    $this->assertSession()->fieldValueEquals('Body', $body);
  }
}
