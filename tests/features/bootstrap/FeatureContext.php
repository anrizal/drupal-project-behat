<?php

/**
 * @file
 * Contains \FeatureContext.
 */

use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines generic step definitions.
 */
class FeatureContext extends RawDrupalContext {
    /**
     * @var \Drupal\node\NodeInterface[]
     */
    protected $nodes = [];

    /**
     * Returns the field alias mapping for news articles.
     *
     * @return array
     */
    protected static function newsFieldAliases() {
        return [
            'title' => 'title',
            'body' => 'body',
            'news article' => 'news_article',
            'News Article' => 'news_article',
        ];
    }

    /**
     * Checks that a 403 Access Denied error occurred.
     *
     * @Then I should get an access denied error
     */
    public function assertAccessDenied() {
        $this->assertSession()->statusCodeEquals(403);
    }

    /**
     * Checks that a given image is present in the page.
     *
     * @Then I (should )see the image :filename
     */
    public function assertImagePresent($filename) {
        // Drupal appends an underscore and a number to the filename when duplicate
        // files are uploaded, for example when a test is run more than once.
        // We split up the filename and extension and match for both.
        $parts = pathinfo($filename);
        $extension = $parts['extension'];
        $filename = $parts['filename'];
        $this->assertSession()->elementExists('css', "img[src$='.$extension'][src*='$filename']");
    }

    /**
     * Checks that a given image is not present in the page.
     *
     * @Then I should not see the image :filename
     */
    public function assertImageNotPresent($filename) {
        // Drupal appends an underscore and a number to the filename when duplicate
        // files are uploaded, for example when a test is run more than once.
        // We split up the filename and extension and match for both.
        $parts = pathinfo($filename);
        $extension = $parts['extension'];
        $filename = $parts['filename'];
        $this->assertSession()->elementNotExists('css', "img[src$='.$extension'][src*='$filename']");
    }


    /**
     * @Then I should see :number news article
     */
    public function assertNewsArticleCount($number) {
        $this->assertSession()->elementsCount('css', 'div.news-article', $number);
    }

    /**
     * Creates a news article.
     *
     * Table format:
     * | title | News title |
     * | body  | Body text. |
     *
     * @Given the following :type article:
     */
    public function givenNewsArticle(TableNode $news_table, $type) {
        $values = $news_table->getRowsHash();
        $values['type'] = $type;
        \Drupal\node\Entity\Node::create($values)->save();
    }

    /**
     * Creates news articles.
     *
     * Table format:
     * | title      | body       |
     * | News title | Body text. |
     *
     * @Given the following :types articles:
     */
    public function givenNewsArticles(TableNode $news_table, $types) {
        $aliases = self::newsFieldAliases();

        foreach ($news_table->getColumnsHash() as $values) {
            $values['type'] = $types;
            $node = \Drupal\node\Entity\Node::create($values);
            $node->save();
            $this->nodes[] = $node;
        }

    }

    /**
     * @AfterScenario
     */
    protected function cleanupNodes() {
        foreach ($this->nodes as $node) {
            $node->delete();
        }
    }

}
