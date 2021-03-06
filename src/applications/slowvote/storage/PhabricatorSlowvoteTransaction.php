<?php

final class PhabricatorSlowvoteTransaction
  extends PhabricatorApplicationTransaction {

  const TYPE_QUESTION     = 'vote:question';
  const TYPE_DESCRIPTION  = 'vote:description';
  const TYPE_RESPONSES    = 'vote:responses';
  const TYPE_SHUFFLE      = 'vote:shuffle';
  const TYPE_CLOSE        = 'vote:close';

  public function getApplicationName() {
    return 'slowvote';
  }

  public function getApplicationTransactionType() {
    return PhabricatorSlowvotePollPHIDType::TYPECONST;
  }

  public function getApplicationTransactionCommentObject() {
    return new PhabricatorSlowvoteTransactionComment();
  }

  public function shouldHide() {
    $old = $this->getOldValue();
    $new = $this->getNewValue();

    switch ($this->getTransactionType()) {
      case PhabricatorSlowvoteTransaction::TYPE_DESCRIPTION:
      case PhabricatorSlowvoteTransaction::TYPE_RESPONSES:
      case PhabricatorSlowvoteTransaction::TYPE_SHUFFLE:
      case PhabricatorSlowvoteTransaction::TYPE_CLOSE:
        return ($old === null);
    }

    return parent::shouldHide();
  }

  public function getTitle() {
    $author_phid = $this->getAuthorPHID();

    $old = $this->getOldValue();
    $new = $this->getNewValue();

    switch ($this->getTransactionType()) {
      case PhabricatorSlowvoteTransaction::TYPE_QUESTION:
        if ($old === null) {
          return pht(
            '%s created this poll.',
            $this->renderHandleLink($author_phid));
        } else {
          return pht(
            '%s changed the poll question from "%s" to "%s".',
            $this->renderHandleLink($author_phid),
            $old,
            $new);
        }
        break;
      case PhabricatorSlowvoteTransaction::TYPE_DESCRIPTION:
        return pht(
          '%s updated the description for this poll.',
          $this->renderHandleLink($author_phid));
      case PhabricatorSlowvoteTransaction::TYPE_RESPONSES:
        // TODO: This could be more detailed
        return pht(
          '%s changed who can see the responses.',
          $this->renderHandleLink($author_phid));
      case PhabricatorSlowvoteTransaction::TYPE_SHUFFLE:
        if ($new) {
          return pht(
            '%s made poll responses appear in a random order.',
            $this->renderHandleLink($author_phid));
        } else {
          return pht(
            '%s made poll responses appear in a fixed order.',
            $this->renderHandleLink($author_phid));
        }
        break;
      case PhabricatorSlowvoteTransaction::TYPE_CLOSE:
        if ($new) {
          return pht(
            '%s closed this poll.',
            $this->renderHandleLink($author_phid));
        } else {
          return pht(
            '%s reopened this poll.',
            $this->renderHandleLink($author_phid));
        }

        break;
    }

    return parent::getTitle();
  }

  public function getIcon() {
    $old = $this->getOldValue();
    $new = $this->getNewValue();

    switch ($this->getTransactionType()) {
      case PhabricatorSlowvoteTransaction::TYPE_QUESTION:
        if ($old === null) {
          return 'fa-plus';
        } else {
          return 'fa-pencil';
        }
      case PhabricatorSlowvoteTransaction::TYPE_DESCRIPTION:
      case PhabricatorSlowvoteTransaction::TYPE_RESPONSES:
        return 'fa-pencil';
      case PhabricatorSlowvoteTransaction::TYPE_SHUFFLE:
        return 'fa-refresh';
      case PhabricatorSlowvoteTransaction::TYPE_CLOSE:
        if ($new) {
          return 'fa-ban';
        } else {
          return 'fa-pencil';
        }
    }

    return parent::getIcon();
  }


  public function getColor() {
    $old = $this->getOldValue();
    $new = $this->getNewValue();

    switch ($this->getTransactionType()) {
      case PhabricatorSlowvoteTransaction::TYPE_QUESTION:
      case PhabricatorSlowvoteTransaction::TYPE_DESCRIPTION:
      case PhabricatorSlowvoteTransaction::TYPE_RESPONSES:
      case PhabricatorSlowvoteTransaction::TYPE_SHUFFLE:
      case PhabricatorSlowvoteTransaction::TYPE_CLOSE:
        return PhabricatorTransactions::COLOR_BLUE;
    }

    return parent::getColor();
  }

  public function hasChangeDetails() {
    switch ($this->getTransactionType()) {
      case PhabricatorSlowvoteTransaction::TYPE_DESCRIPTION:
        return true;
    }
    return parent::hasChangeDetails();
  }

  public function renderChangeDetails(PhabricatorUser $viewer) {
    return $this->renderTextCorpusChangeDetails(
      $viewer,
      $this->getOldValue(),
      $this->getNewValue());
  }


}
