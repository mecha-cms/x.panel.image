<?php

namespace x\panel\lot\type\field {
    function image($value, $key) {
        $type = \strtr($value['type'] ?? "", '/', "\\");
        if (0 === \strpos($type . "\\", $prefix = "field\\image\\")) {
            $type = \substr($type, \strlen($prefix));
        }
        return \call_user_func(__FUNCTION__ . "\\" . ("" !== $type ? $type : 'blob'), $value, $key);
    }
}

namespace x\panel\lot\type\field\image {
    function blob($value, $key) {
        \extract(\lot(), \EXTR_SKIP);
        $image = (array) ($value['state'] ?? []);
        $image_height = $image['height'] ?? 72;
        $image_image = $image['image'] ?? null;
        $image_link = $image['link'] ?? null;
        $image_path = $image['path'] ?? null;
        $image_route = $image['route'] ?? null;
        $image_size = $image['size'] ?? null;
        $image_type = $image['type'] ?? null;
        $image_url = $image['url'] ?? null;
        $image_width = $image['width'] ?? 72;
        $is_link = 0 === \strpos($v = $value['value'] ?? "", '//') || 0 === \strpos($v, 'data:image/') || false !== \strpos($v, '://');
        $is_vital = $value['vital'] ?? $value['is']['vital'] ?? false;
        if ($image_path && \is_file($image_path = \stream_resolve_include_path($image_path))) {
            $image_url = \To::URL($image_path) . '?v=' . \filemtime($image_path);
        } else if ($image_link) {
            $image_url = \long($image_link);
            $is_link = true;
        } else if ($image_route) {
            if (\is_file($image_path = \stream_resolve_include_path(\PATH . $image_route))) {
                $image_url = \To::URL($image_path) . '?v=' . \filemtime($image_path);
            } else {
                $image_url = \long($image_route);
                $is_link = true;
            }
        } else if ($image_url && \is_file($image_path = \To::path($image_url))) {
            $image_url = \To::URL($image_path) . '?v=' . \filemtime($image_path);
        }
        if ($image_url) {
            $v = \strtok($image_url, '?&#');
            $image_name = $image['name'] ?? \pathinfo($v, \PATHINFO_FILENAME);
            $image_x = $image['x'] ?? \pathinfo($v, \PATHINFO_EXTENSION);
        } else {
            $image_name = $image['name'] ?? null;
            $image_x = $image['x'] ?? null;
        }
        $value['field'][2]['accept'] = $value['field'][2]['accept'] ?? 'image/*';
        if ($image_url && 'get' === $_['task']) {
            $value['field-exit'] = \trim('

<div class="content:rows p">
  <div class="content:row">
    <div class="content:columns has:gap">
      <div class="content:column" style="max-width: 112px;">
        <div class="figure" style="align-items: center; display: flex; height: 112px; justify-content: center; padding: 0; width: 112px;">
          <img alt="" height="' . \eat($image_height) . '" src="' . \eat($image_image ?? $image_url) . '" style="display: block;" width="' . \eat($image_width) . '">
        </div>
      </div>
      <div class="content:column">
        <table>
          <colgroup>
            <col style="width: 20%;">
          </colgroup>
          <tbody>
            <tr>
              <th scope="row">' . \i('Name') . '</th>
              <td>
                <a href="' . \eat(0 === \strpos($image_url ?? "", $url . '/lot/') ? \x\panel\to\link([
                    'hash' => null,
                    'part' => 0,
                    'path' => \substr(\strtok($image_url, '?&#'), \strlen($url . '/lot/')),
                    'query' => null,
                    'task' => 'get'
                ]) : $image_url) . '" target="_blank" title="' . \eat(\i($is_link ? 'View' : 'Edit')) . '">' . ($image_name . ("" !== (string) $image_x ? '.' . $image_x : "")) . '</a>
              </td>
            </tr>
            <tr>
              <th scope="row">' . \i('Size') . '</th>
              <td>' . $image_size . '</td>
            </tr>
            <tr>
              <th scope="row">' . \i('Type') . '</th>
              <td>' . $image_type . '</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

            ');
        }
        $value['is']['link'] = $value['is']['link'] ?? $is_link;
        $value['type'] = 'field/blob';
        unset($value['value']);
        return \x\panel\lot\type($value, $key);
    }
    function link($value, $key) {
        \extract(\lot(), \EXTR_SKIP);
        $image = (array) ($value['state'] ?? []);
        $image_height = $image['height'] ?? 72;
        $image_image = $image['image'] ?? null;
        $image_link = $image['link'] ?? null;
        $image_path = $image['path'] ?? null;
        $image_route = $image['route'] ?? null;
        $image_size = $image['size'] ?? null;
        $image_type = $image['type'] ?? null;
        $image_url = $image['url'] ?? null;
        $image_width = $image['width'] ?? 72;
        $is_link = 0 === \strpos($v = $value['value'] ?? "", '//') || 0 === \strpos($v, 'data:image/') || false !== \strpos($v, '://');
        $is_vital = $value['vital'] ?? $value['is']['vital'] ?? false;
        if ($image_path && \is_file($image_path = \stream_resolve_include_path($image_path))) {
            $image_url = \To::URL($image_path) . '?v=' . \filemtime($image_path);
        } else if ($image_link) {
            $image_url = \long($image_link);
            $is_link = true;
        } else if ($image_route) {
            if (\is_file($image_path = \stream_resolve_include_path(\PATH . $image_route))) {
                $image_url = \To::URL($image_path) . '?v=' . \filemtime($image_path);
            } else {
                $image_url = \long($image_route);
                $is_link = true;
            }
        } else if ($image_url && \is_file($image_path = \To::path($image_url))) {
            $image_url = \To::URL($image_path) . '?v=' . \filemtime($image_path);
        }
        if ($image_url) {
            $v = \strtok($image_url, '?&#');
            $image_name = $image['name'] ?? \pathinfo($v, \PATHINFO_FILENAME);
            $image_x = $image['x'] ?? \pathinfo($v, \PATHINFO_EXTENSION);
        } else {
            $image_name = $image['name'] ?? null;
            $image_x = $image['x'] ?? null;
        }
        if ($image_url && 'get' === $_['task']) {
            $value['field-exit'] = \trim('

<div class="content:rows p">
  <div class="content:row">
    <div class="content:columns has:gap">
      <div class="content:column" style="max-width: 112px;">
        <div class="figure" style="align-items: center; display: flex; height: 112px; justify-content: center; padding: 0; width: 112px;">
          <img alt="" height="' . \eat($image_height) . '" src="' . \eat($image_image ?? $image_url) . '" style="display: block;" width="' . \eat($image_width) . '">
        </div>
      </div>
      <div class="content:column">
        <table>
          <colgroup>
            <col style="width: 20%;">
          </colgroup>
          <tbody>
            <tr>
              <th scope="row">' . \i('Name') . '</th>
              <td>
                <a href="' . \eat(0 === \strpos($image_url ?? "", $url . '/lot/') ? \x\panel\to\link([
                    'hash' => null,
                    'part' => 0,
                    'path' => \substr(\strtok($image_url, '?&#'), \strlen($url . '/lot/')),
                    'query' => null,
                    'task' => 'get'
                ]) : $image_url) . '" target="_blank" title="' . \eat(\i($is_link ? 'View' : 'Edit')) . '">' . ($image_name . ("" !== (string) $image_x ? '.' . $image_x : "")) . '</a>
              </td>
            </tr>
            <tr>
              <th scope="row">' . \i('Size') . '</th>
              <td>' . $image_size . '</td>
            </tr>
            <tr>
              <th scope="row">' . \i('Type') . '</th>
              <td>' . $image_type . '</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

            ');
        }
        $value['hint'] = $value['hint'] ?? \strtr(\strtr($state->x->{'panel.image'}->folder ?? '/lot/asset/user/' . $user->name, ['/' => \D]), [
            \PATH . \D => '/',
            \D => '/'
        ]) . '/' . \i('image') . '.jpg';
        $value['tasks'] = \array_replace_recursive([
            'let' => [
                'description' => $is_link || $is_vital ? 'View' : 'Delete',
                'icon' => $is_link || $is_vital ? 'M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z' : 'M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19M8,9H16V19H8V9M15.5,4L14.5,3H9.5L8.5,4H5V6H19V4H15.5Z',
                'skip' => !$image_url || 'set' === $_['task'],
                'link' => $is_link || $is_vital ? $image_url : null,
                'stack' => 10,
                'title' => false,
                'url' => $is_link || $is_vital ? null : [
                    'part' => 0,
                    'path' => \substr(\strtok($image_url ?? "", '?&#'), \strlen($url . '/lot/')),
                    'query' => [
                        'kick' => $url->current,
                        'token' => $_['token'],
                        'trash' => !empty($state->x->panel->trash) ? \date('Y-m-d-H-i-s') : null
                    ],
                    'task' => 'let'
                ]
            ]
        ], $value['tasks'] ?? []);
        $value['is']['link'] = $value['is']['link'] ?? $is_link;
        $value['pattern'] = $value['pattern'] ?? "(data:image/(apng|avif|gif|jpeg|png|svg\\+xml|webp);base64,|(https?:)?\\/\\/|[.]{0,2}\\/)[^\\/]\\S*";
        $value['type'] = 'field/link';
        return \x\panel\lot\type($value, $key);
    }
}