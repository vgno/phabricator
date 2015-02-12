<?php

final class ExampleCustomField extends ManiphestCustomField {

  public function getFieldKey() {
    return 'example:test';
  }

  public function shouldAppearInPropertyView() {
    return true;
  }

  public function renderPropertyViewLabel() {
    return pht('Dependency Graph');
  }

  public function renderPropertyViewValue(array $handles) {
    $task = $this->getObject();

    $edge_type = PhabricatorEdgeConfig::TYPE_TASK_DEPENDS_ON_TASK;

    $graph = id(new PhabricatorEdgeGraph())
      ->setEdgeType($edge_type)
      ->addNodes(
        array(
          '<seed>' => array($task->getPHID()),
        ))
      ->loadGraph();

    $nodes = $graph->getNodes();
    unset($nodes['<seed>']);

    if (count($nodes) == 1) {
      return null;
    }

    $phids = array_keys($nodes);
    $handles = id(new PhabricatorHandleQuery())
      ->setViewer($this->getViewer())
      ->withPHIDs($phids)
      ->execute();

    return $this->drawNodes($task->getPHID(), $nodes, $handles, true);
  }

  private function drawNodes($phid, $nodes, $handles, $is_top = false) {
    $content = array();
    if (!$is_top) {
      $content[] = phutil_tag('li', array(), $handles[$phid]->renderLink());
    }

    foreach ($nodes[$phid] as $other) {
      $content[] = phutil_tag(
        'li',
        array(
          'style' => $is_top ? null : 'padding-left: 16px;',
        ),
        $this->drawNodes($other, $nodes, $handles));
    }

    return phutil_tag('ul', array(), $content);
  }

}
