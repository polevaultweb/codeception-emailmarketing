<?php

namespace Codeception\Module;

use Codeception\Module;
use PHPUnit\Framework\Assert;

abstract class EmailMarketing extends Module {

	/**
	 * Get a subscriber by email address.
	 *
	 * @param string $email
	 *
	 * @return mixed
	 */
	protected abstract function getSubscriber( $email );

	/**
	 * Get the campaigns for a subscriber.
	 *
	 * @param string      $email
	 * @param null|string $status Status of the subscription, eg. active
	 *
	 * @return mixed
	 */
	public abstract function getCampaignsForSubscriber( $email, $status = null );

	/**
	 * Get the tags for a subscriber.
	 *
	 * @param string $email
	 *
	 * @return array
	 */
	public abstract function getTagsForSubscriber( $email );

	/**
	 * Get the value of a custom field for a subscriber.
	 *
	 * @param string $email
	 * @param string $field_name
	 *
	 * @return mixed
	 */
	protected abstract function getSubscriberCustomField( $email, $field_name );

	/**
	 * Delete a subscriber.
	 *
	 * @param string $email
	 *
	 * @return mixed
	 */
	public abstract function deleteSubscriber( $email );

	/**
	 * @param int $timeout_in_second
	 * @param int $interval_in_millisecond
	 *
	 * @return ModuleWait
	 */
	protected function wait( $timeout_in_second = 30, $interval_in_millisecond = 250 ) {
		return new ModuleWait( $this, $timeout_in_second, $interval_in_millisecond );
	}

	public function seeCustomFieldForSubscriber( $email, $name, $value ) {
		$current_value = $this->getSubscriberCustomField( $email, $name );

		Assert::assertEquals( $current_value, $value );
	}

	public function seeTagsForSubscriber( $email, $tags ) {
		$subscriber_tags = $this->getTagsForSubscriber( $email );

		if ( ! is_array( $tags ) ) {
			$tags = array( $tags );
		}

		sort( $tags );
		sort( $subscriber_tags );

		Assert::assertTrue( ! array_diff( $tags, $subscriber_tags ) );
	}

	public function cantSeeTagsForSubscriber( $email, $tags ) {
		$subscriber_tags = $this->getTagsForSubscriber( $email );

		if ( ! is_array( $tags ) ) {
			$tags = array( $tags );
		}

		foreach ( $tags as $tag ) {
			Assert::assertFalse( in_array( $tag, $subscriber_tags ) );
		}
	}

	public function seeCampaignsForSubscriber( $email, $campaign_ids, $status = 'active' ) {
		$campaigns = $this->getCampaignsForSubscriber( $email, $status );
		if ( false === $campaigns ) {
			Assert::fail( 'Subscriber not found' );
		}

		if ( ! is_array( $campaign_ids ) ) {
			$campaign_ids = array( $campaign_ids );
		}

		sort( $campaign_ids );
		sort( $campaigns );


		Assert::assertTrue( ! array_diff( $campaign_ids, $campaigns ) );
	}

	public function cantSeeCampaignsForSubscriber( $email, $campaign_ids, $status = 'active' ) {
		$campaigns = $this->getCampaignsForSubscriber( $email, $status );
		if ( false === $campaigns ) {
			Assert::fail( 'Subscriber not found' );
		}

		if ( ! is_array( $campaign_ids ) ) {
			$campaign_ids = array( $campaign_ids );
		}

		foreach ( $campaign_ids as $campaign_id ) {
			Assert::assertFalse( in_array( $campaign_id, $campaigns ) );
		}
	}

	/**
	 * Wait until a subscriber doesn't have certain tags.
	 *
	 * @param string $email
	 * @param array  $tags
	 * @param int    $timeout
	 *
	 * @throws \Exception
	 */
	public function waitForSubscriberToNotHaveTags( $email, $tags, $timeout = 5 ) {
		if ( ! is_array( $tags ) ) {
			$tags = array( $tags );
		}
		$condition = function () use ( $email, $tags ) {
			$subscriber_tags = $this->getTagsForSubscriber( $email );

			$count = 0;
			foreach ( $tags as $tag ) {
				$constraint = Assert::isFalse();
				if ( $constraint->evaluate( in_array( $tag, $subscriber_tags ), '', true ) ) {
					$count ++;
				}
			}

			if ( $count === count( $tags ) ) {
				return true;
			}

			return false;
		};

		$message = sprintf( 'Waited for %d secs but the subscriber still has one or more of the tags %s', $timeout, implode( ',', $tags ) );

		$this->wait( $timeout )->until( $condition, $message );
	}
}