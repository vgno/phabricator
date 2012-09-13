<?php

/*
 * Copyright 2012 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

final class AphrontMoreView extends AphrontView {

  private $some;
  private $more;
  private $expandtext;

  public function setSome($some) {
    $this->some = $some;
    return $this;
  }

  public function setMore($more) {
    $this->more = $more;
    return $this;
  }

  public function setExpandText($text) {
    $this->expandtext = $text;
    return $this;
  }

  public function render() {
    $some = $this->some;

    $text = "(Show More\xE2\x80\xA6)";
    if ($this->expandtext !== null) {
      $text = $this->expandtext;
    }

    $link = null;
    if ($this->more && $this->more != $this->some) {
      Javelin::initBehavior('aphront-more');
      $link = ' '.javelin_render_tag(
        'a',
        array(
          'sigil'       => 'aphront-more-view-show-more',
          'mustcapture' => true,
          'href'        => '#',
          'meta'        => array(
            'more' => $this->more,
          ),
        ),
        $text);
    }

    return javelin_render_tag(
      'div',
      array(
        'sigil' => 'aphront-more-view',
      ),
      $some.$link);
  }
}
