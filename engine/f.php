<?php

namespace x\panel\type\field {
    function image($value, $key) {
        $type = \strtr($value['type'] ?? "", '/', "\\");
        if (0 === \strpos($type . "\\", $prefix = "field\\image\\")) {
            $type = \substr($type, \strlen($prefix));
        }
        return \call_user_func(__FUNCTION__ . "\\" . ("" !== $type ? $type : 'blob'), $value, $key);
    }
    function images($value, $key) {} // TODO
}

namespace x\panel\type\field\image {
    function blob($value, $key) {
        \extract($GLOBALS, \EXTR_SKIP);
        $image = $value['value'] ?? "";
        $key = $value['key'] ?? $key;
        $link = 0 === \strpos($image, '//') || 0 === \strpos($image, 'data:image/') || false !== \strpos($image, '://');
        $page = new \Page($value['file'] ?? null);
        $file = $image && !$link && \is_file($f = \PATH . $image) ? \path($f) : false;
        if (isset($state->x->image)) {
            if ($file) {
                $value['field-exit'] = \trim('
<div class="content:rows p">
  <div class="content:row">
    <div class="content:columns has:gap">
      <div class="content:column" style="max-width: 112px;">
        <div class="figure" style="align-items: center; display: flex; height: 112px; justify-content: center; padding: 0; width: 112px;">
          <img alt="" height="72" src="' . $page->{$key}(72, 72, 100) . '?v=' . \filemtime($file) . '" style="display: block;" width="72">
        </div>
      </div>
      <div class="content:column">
        <table>
          <tbody>
            <tr>
              <th scope="row">' . \i('Name') . '</th>
              <td>
                <a href="' . \eat(0 === \strpos($image, '/lot/') ? \x\panel\to\link([
                    'hash' => null,
                    'part' => 0,
                    'path' => \substr($image, \strlen('/lot/')),
                    'query' => null,
                    'task' => 'get'
                ]) : \long($image)) . '" target="_blank" title="' . \eat(\i('Edit')) . '">' . \basename($image) . '</a>
              </td>
            </tr>
            <tr>
              <th scope="row">' . \i('Size') . '</th>
              <td>' . \image($file)->size . '</td>
            </tr>
            <tr>
              <th scope="row">' . \i('Type') . '</th>
              <td>' . \image($file)->type . '</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
');
            } else if ($v = $page->{$key}) {
                $value['field-exit'] = \trim('
<div class="content:rows p">
  <div class="content:row">
    <div class="content:columns has:gap">
      <div class="content:column" style="max-width: 112px;">
        <div class="figure" style="align-items: center; display: flex; height: 112px; justify-content: center; padding: 0; width: 112px;">
          <img alt="" height="72" src="' . \eat($page->{$key}(72, 72, 100)) . '" style="display: block;" width="72">
        </div>
      </div>
      <div class="content:column">
        <table>
          <tbody>
            <tr>
              <th scope="row">' . \i('Name') . '</th>
              <td>
                <a href="' . \eat($v) . '" target="_blank" title="' . \eat(\i('View')) . '">' . \basename(\strtok($v, '?&#')) . '</a>
              </td>
            </tr>
            <tr>
              <th scope="row">' . \i('Size') . '</th>
              <td>-</td>
            </tr>
            <tr>
              <th scope="row">' . \i('Type') . '</th>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
');
            }
        }
        $value['type'] = 'field/blob';
        unset($value['value']);
        return \x\panel\type($value, $key);
    }
    function link($value, $key) {
        \extract($GLOBALS, \EXTR_SKIP);
        $image = $value['value'] ?? "";
        $key = $value['key'] ?? $key;
        $link = 0 === \strpos($image, '//') || 0 === \strpos($image, 'data:image/') || false !== \strpos($image, '://');
        $page = new \Page($value['file'] ?? null);
        $vital = !empty($state->x->{'panel.image'}->vital);
        $file = $image && !$link && \is_file($f = \PATH . $image) ? \path($f) : false;
        if (isset($state->x->image)) {
            if ($file) {
                $value['field-exit'] = \trim('
<div class="content:rows p">
  <div class="content:row">
    <div class="content:columns has:gap">
      <div class="content:column" style="max-width: 112px;">
        <div class="figure" style="align-items: center; display: flex; height: 112px; justify-content: center; padding: 0; width: 112px;">
          <img alt="" height="72" src="' . $page->{$key}(72, 72, 100) . '?v=' . \filemtime($file) . '" style="display: block;" width="72">
        </div>
      </div>
      <div class="content:column">
        <table>
          <tbody>
            <tr>
              <th scope="row">' . \i('Name') . '</th>
              <td>
                <a href="' . \eat(0 === \strpos($image, '/lot/') ? \x\panel\to\link([
                    'hash' => null,
                    'part' => 0,
                    'path' => \substr($image, \strlen('/lot/')),
                    'query' => null,
                    'task' => 'get'
                ]) : \long($image)) . '" target="_blank" title="' . \eat(\i('Edit')) . '">' . \basename($image) . '</a>
              </td>
            </tr>
            <tr>
              <th scope="row">' . \i('Size') . '</th>
              <td>' . \image($file)->size . '</td>
            </tr>
            <tr>
              <th scope="row">' . \i('Type') . '</th>
              <td>' . \image($file)->type . '</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
');
            } else if ($v = $page->{$key}) {
                $value['field-exit'] = \trim('
<div class="content:rows p">
  <div class="content:row">
    <div class="content:columns has:gap">
      <div class="content:column" style="max-width: 112px;">
        <div class="figure" style="align-items: center; display: flex; height: 112px; justify-content: center; padding: 0; width: 112px;">
          <img alt="" height="72" src="' . \eat($page->{$key}(72, 72, 100)) . '" style="display: block;" width="72">
        </div>
      </div>
      <div class="content:column">
        <table>
          <tbody>
            <tr>
              <th scope="row">' . \i('Name') . '</th>
              <td>
                <a href="' . \eat($v) . '" target="_blank" title="' . \eat(\i('View')) . '">' . \basename(\strtok($v, '?&#')) . '</a>
              </td>
            </tr>
            <tr>
              <th scope="row">' . \i('Size') . '</th>
              <td>-</td>
            </tr>
            <tr>
              <th scope="row">' . \i('Type') . '</th>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
');
            }
        }
        $value['hint'] = $value['hint'] ?? \strtr(\strtr($state->x->{'panel.image'}->folder ?? '/lot/asset/user/' . $user->name, ['/' => \D]), [
            \PATH . \D => '/',
            \D => '/'
        ]) . '/image.jpg';
        $value['icon'] = $value['icon'] ?? [null, $image && 'set' !== $_['task'] ? [
            'd' => $link || $vital ? 'M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z' : 'M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19M8,9H16V19H8V9M15.5,4L14.5,3H9.5L8.5,4H5V6H19V4H15.5Z',
            'description' => $link || $vital ? 'View' : 'Delete',
            'link' => $link || $vital ? $image : null,
            'url' => $link || $vital ? null : [
                'part' => 0,
                'path' => \substr($image, \strlen('/lot/')),
                'query' => [
                    'kick' => $url->current,
                    'token' => $_['token'],
                    'trash' => !empty($state->x->panel->trash) ? \date('Y-m-d-H-i-s') : null
                ],
                'task' => 'let'
            ]
        ] : null];
        $value['pattern'] = $value['pattern'] ?? "^(data:image/(apng|avif|gif|jpeg|png|svg\\+xml|webp);base64,|(https?:)?\\/\\/|[.]{0,2}\\/)[^\\/]\\S*$";
        return \x\panel\type\field\u_r_l($value, $key);
    }
}

namespace x\panel\type\field\images {
    function blob($value, $key) {} // TODO
    function blobs($value, $key) {} // TODO
    function link($value, $key) {} // TODO
}