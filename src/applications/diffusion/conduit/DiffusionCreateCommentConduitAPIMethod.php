<?php

final class DiffusionCreateCommentConduitAPIMethod
  extends DiffusionConduitAPIMethod {

  public function getAPIMethodName() {
    return 'diffusion.createcomment';
  }

  public function getMethodStatus() {
    return self::METHOD_STATUS_DEPRECATED;
  }

  public function getMethodDescription() {
    return 'Add a comment to a Diffusion commit. By specifying an action of '.
           '"concern", "accept", "resign", or "close", auditing actions can '.
           'be triggered. Defaults to "comment".';
  }

  public function defineParamTypes() {
    return array(
      'phid'    => 'required string',
      'action'  => 'optional string',
      'message' => 'required string',
      'silent'  => 'optional bool',
    );
  }

  public function defineReturnType() {
    return 'bool';
  }

  public function defineErrorTypes() {
    return array(
      'ERR_BAD_COMMIT' => 'No commit found with that PHID',
      'ERR_BAD_ACTION' => 'Invalid action type',
      'ERR_MISSING_MESSAGE' => 'Message is required',
    );
  }

  protected function execute(ConduitAPIRequest $request) {
    $commit_phid = $request->getValue('phid');
    $commit = id(new DiffusionCommitQuery())
      ->setViewer($request->getUser())
      ->withPHIDs(array($commit_phid))
      ->needAuditRequests(true)
      ->executeOne();
    if (!$commit) {
      throw new ConduitException('ERR_BAD_COMMIT');
    }

    $message = trim($request->getValue('message'));
    if (!$message) {
      throw new ConduitException('ERR_MISSING_MESSAGE');
    }

    $action = $request->getValue('action');
    if (!$action) {
      $action = PhabricatorAuditActionConstants::COMMENT;
    }

    // Disallow ADD_CCS, ADD_AUDITORS for now
    if (!in_array($action, array(
      PhabricatorAuditActionConstants::CONCERN,
      PhabricatorAuditActionConstants::ACCEPT,
      PhabricatorAuditActionConstants::COMMENT,
      PhabricatorAuditActionConstants::RESIGN,
      PhabricatorAuditActionConstants::CLOSE,
    ))) {
      throw new ConduitException('ERR_BAD_ACTION');
    }

    $comments = array();

    if ($action != PhabricatorAuditActionConstants::COMMENT) {
      $comments[] = id(new PhabricatorAuditComment())
        ->setAction($action);
    }

    if (strlen($message)) {
      $comments[] = id(new PhabricatorAuditComment())
        ->setAction(PhabricatorAuditActionConstants::COMMENT)
        ->setContent($message);
    }

    id(new PhabricatorAuditCommentEditor($commit))
      ->setActor($request->getUser())
      ->setNoEmail($request->getValue('silent'))
      ->addComments($comments);

    return true;
    // get the full uri of the comment?
    // i.e, PhabricatorEnv::getURI(rXX01ab23cd#comment-9)
  }

}
