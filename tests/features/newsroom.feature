@api
Feature: News Creation
  In order to have meaningful content
  As a authenticated users
  I want to make sure only I can create article

Scenario: Anonymous user can see the news overview
  Given the following news_article articles:
#    | title     | Article 1           |
#    | body      | The first article.  |
    | title     | body                |
    | Article 1 | The first article.  |
    | Article 2 | The second article. |
    | Article 3 | The third article.  |

  Given I am not logged in
  When I visit "news"
  Then I should see the heading "News"
  And I should see 3 news article
  And I should see the link "Article 1"
  And I should see the text "The first article."
  And I should see the link "Article 2"
  And I should see the text "The second article."
  And I should see the link "Article 3"
  And I should see the text "The third article."

