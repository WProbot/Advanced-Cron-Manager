<?php
/**
 * Add event form
 */

$schedules = $this->get_var( 'schedules' );
$single_schedule = $this->get_var( 'single_schedule' );

?>

<?php $this->get_view( 'forms/header' ); ?>

<?php wp_nonce_field( 'acm/event/insert', 'nonce', false ); ?>

<label for="event-hook"><?php esc_html_e( 'Hook' ); ?></label>
<p class="description"><?php esc_html_e( 'Should contain only lowercase letters, numbers and _' ); ?></p>
<input type="text" id="event-hook" name="hook" class="widefat">

<label for="event-execution"><?php esc_html_e( 'First execution' ); ?></label>
<p class="description"><?php esc_html_e( 'When past date will be provided or left empty, event will be executed in the next queue' ); ?></p>
<input type="datetime-local" id="event-execution" name="execution" class="widefat"></input>
<input type="hidden" id="event-offset" name="execution_offset"></input>

<label for="event-schedule"><?php esc_html_e( 'Schedule' ); ?></label>
<p class="description"><?php esc_html_e( 'After first execution repeat:' ); ?></p>
<select id="event-schedule" class="widefat" name="schedule">
	<option value="<?php echo esc_attr( $single_schedule->slug ); ?>">
		<?php echo esc_html( sprintf( __( 'Don\'t repeat (%s)' ), $single_schedule->label ) ); ?>
	</option>
	<?php foreach ( $schedules as $schedule ): ?>
		<option value="<?php echo esc_attr( $schedule->slug ); ?>"><?php echo esc_html( $schedule->label ); ?></option>
	<?php endforeach ?>
</select>

<label>Arguments</label>
<p class="description"><?php esc_html_e( 'New inputs will be added automatically when you type' ); ?></p>
<div class="event-arguments">
	<input type="text" name="arguments[]" class="event-argument widefat">
</div>

<?php $this->get_view( 'forms/footer' ); ?>
