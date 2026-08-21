<?php
/**
 * Archive sort + pagination query helpers.
 *
 * @package LBDS
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Allowed sort keys.
 *
 * @return array<string, array{label:string,orderby:string,order:string}>
 */
function lbds_sort_options(): array {
	return array(
		'newest'     => array(
			'label'   => __('Nieuwste eerst', 'lbds'),
			'orderby' => 'date',
			'order'   => 'DESC',
		),
		'oldest'     => array(
			'label'   => __('Oudste eerst', 'lbds'),
			'orderby' => 'date',
			'order'   => 'ASC',
		),
		'title_asc'  => array(
			'label'   => __('Titel A–Z', 'lbds'),
			'orderby' => 'title',
			'order'   => 'ASC',
		),
		'title_desc' => array(
			'label'   => __('Titel Z–A', 'lbds'),
			'orderby' => 'title',
			'order'   => 'DESC',
		),
		'popular'    => array(
			'label'   => __('Meest besproken', 'lbds'),
			'orderby' => 'comment_count',
			'order'   => 'DESC',
		),
	);
}

function lbds_current_sort(): string {
	$sort = isset($_GET['lbds_sort']) ? sanitize_key((string) $_GET['lbds_sort']) : 'newest'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$opts = lbds_sort_options();
	return isset($opts[$sort]) ? $sort : 'newest';
}

/**
 * Apply sort on category + blog listings; limit search to posts only.
 *
 * @param WP_Query $query Query.
 */
function lbds_pre_get_posts(WP_Query $query): void {
	if (is_admin() || !$query->is_main_query()) {
		return;
	}

	// Search should list articles only — never static pages (Home, Privacy, etc.).
	if ($query->is_search()) {
		$query->set('post_type', 'post');
		return;
	}

	if (!$query->is_category() && !$query->is_home() && !$query->is_tag() && !$query->is_author()) {
		return;
	}

	$opts = lbds_sort_options();
	$key  = lbds_current_sort();
	$query->set('orderby', $opts[$key]['orderby']);
	$query->set('order', $opts[$key]['order']);
}
add_action('pre_get_posts', 'lbds_pre_get_posts');

/**
 * URL for a sort option on the current archive.
 */
function lbds_sort_url(string $key): string {
	$url = remove_query_arg(array('lbds_sort', 'paged'));
	if ($key === 'newest') {
		return $url;
	}
	return add_query_arg('lbds_sort', $key, $url);
}

/**
 * Render sort dropdown (Masterblog .sort).
 */
function lbds_the_sort_select(): void {
	$current = lbds_current_sort();
	$opts    = lbds_sort_options();
	?>
	<div class="sort">
		<label for="lbds-sort"><?php esc_html_e('Sorteren op', 'lbds'); ?></label>
		<select id="lbds-sort" name="lbds_sort" onchange="if(this.value){window.location.href=this.value;}">
			<?php foreach ($opts as $key => $opt) : ?>
				<option value="<?php echo esc_url(lbds_sort_url($key)); ?>" <?php selected($current, $key); ?>>
					<?php echo esc_html($opt['label']); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
}

/**
 * Render pagination matching Category.html (.pagination / .page-btn / .page-arrow).
 */
function lbds_the_pagination(): void {
	global $wp_query;
	$total = (int) $wp_query->max_num_pages;
	if ($total <= 1) {
		return;
	}

	$current = max(1, (int) get_query_var('paged'));
	if ($current < 1) {
		$current = 1;
	}

	$add_args = array();
	$sort     = lbds_current_sort();
	if ($sort !== 'newest') {
		$add_args['lbds_sort'] = $sort;
	}

	$links = paginate_links(
		array(
			'total'     => $total,
			'current'   => $current,
			'mid_size'  => 1,
			'end_size'  => 1,
			'type'      => 'array',
			'prev_next' => true,
			'prev_text' => 'PREV',
			'next_text' => 'NEXT',
			'add_args'  => $add_args,
		)
	);

	if (!is_array($links) || !$links) {
		return;
	}

	$prev_svg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>';
	$next_svg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>';

	echo '<nav class="pagination" aria-label="' . esc_attr__('Paginering', 'lbds') . '">';

	foreach ($links as $link) {
		$is_prev    = str_contains($link, 'PREV') || str_contains($link, 'prev page-numbers');
		$is_next    = str_contains($link, 'NEXT') || str_contains($link, 'next page-numbers');
		$is_current = str_contains($link, 'current');
		$is_dots    = str_contains($link, 'dots') || str_contains($link, '&hellip;') || str_contains($link, '…');

		$href = '';
		if (preg_match('/href=[\'"]([^\'"]+)[\'"]/', $link, $m)) {
			$href = $m[1];
		}

		if ($is_prev) {
			if ($href) {
				echo '<a class="page-arrow" href="' . esc_url($href) . '">' . $prev_svg . esc_html__('Vorige', 'lbds') . '</a>'; // phpcs:ignore
			} else {
				echo '<span class="page-arrow is-disabled" aria-disabled="true">' . $prev_svg . esc_html__('Vorige', 'lbds') . '</span>'; // phpcs:ignore
			}
			continue;
		}
		if ($is_next) {
			if ($href) {
				echo '<a class="page-arrow" href="' . esc_url($href) . '">' . esc_html__('Volgende', 'lbds') . $next_svg . '</a>'; // phpcs:ignore
			} else {
				echo '<span class="page-arrow is-disabled" aria-disabled="true">' . esc_html__('Volgende', 'lbds') . $next_svg . '</span>'; // phpcs:ignore
			}
			continue;
		}
		if ($is_dots) {
			echo '<span class="page-btn" aria-hidden="true">&hellip;</span>';
			continue;
		}

		$num = trim(wp_strip_all_tags($link));
		if ($is_current || !$href) {
			echo '<span class="page-btn active" aria-current="page">' . esc_html($num) . '</span>';
		} else {
			echo '<a class="page-btn" href="' . esc_url($href) . '">' . esc_html($num) . '</a>';
		}
	}

	echo '</nav>';
}
