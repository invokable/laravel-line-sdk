<?php

namespace Tests\Notifications;

use LINE\Clients\MessagingApi\Model\AllMentionTarget;
use LINE\Clients\MessagingApi\Model\EmojiSubstitutionObject;
use LINE\Clients\MessagingApi\Model\FlexBubble;
use LINE\Clients\MessagingApi\Model\FlexCarousel;
use LINE\Clients\MessagingApi\Model\FlexMessage;
use LINE\Clients\MessagingApi\Model\LocationMessage;
use LINE\Clients\MessagingApi\Model\MentionSubstitutionObject;
use LINE\Clients\MessagingApi\Model\QuickReply;
use LINE\Clients\MessagingApi\Model\TextMessageV2;
use LINE\Clients\MessagingApi\Model\UserMentionTarget;
use LINE\Constants\MentioneeType;
use LINE\Constants\MessageType;
use Revolution\Line\Notifications\LineMessage;
use Tests\TestCase;

class LineMessageTest extends TestCase
{
    public function test_line_message()
    {
        $message = LineMessage::create('test1')
            ->text('test2')
            ->sticker(package: 1, sticker: 2)
            ->image(original: '', preview: '')
            ->video(original: '', preview: '')
            ->with([
                'notificationDisabled' => false,
            ]);

        $this->assertArrayHasKey('messages', $message->toArray());
        $this->assertArrayHasKey('notificationDisabled', $message->toArray());
    }

    public function test_location_message()
    {
        $location = (new LocationMessage)
            ->setType(MessageType::LOCATION)
            ->setTitle('title')
            ->setAddress('address')
            ->setLatitude(0.0)
            ->setLongitude(0.0);

        $message = (new LineMessage)
            ->text('text')
            ->message($location);

        $this->assertArrayHasKey('messages', $message->toArray());
        $this->assertInstanceOf(LocationMessage::class, $message->toArray()['messages'][1]);
    }

    public function test_create_with_sender()
    {
        $message = LineMessage::create(text: 'test', name: 'name', icon: 'icon');

        $sender = $message->toArray()['messages'][0]->getSender();
        $this->assertSame('name', $sender->getName());
        $this->assertSame('icon', $sender->getIconUrl());
    }

    public function test_sender()
    {
        $message = (new LineMessage)
            ->withSender(name: 'name', icon: 'icon')
            ->text('test');

        $sender = $message->toArray()['messages'][0]->getSender();
        $this->assertSame('name', $sender->getName());
        $this->assertSame('icon', $sender->getIconUrl());
    }

    public function test_sender_wrong_order()
    {
        $message = (new LineMessage)
            ->text('test')
            ->withSender(name: 'name', icon: 'icon');

        $sender = $message->toArray()['messages'][0]->getSender();
        $this->assertNull($sender);
    }

    public function test_sender_name()
    {
        $message = (new LineMessage)
            ->withSender(name: 'name')
            ->text('test');

        $this->assertSame('name', $message->toArray()['messages'][0]->getSender()->getName());
    }

    public function test_sender_icon()
    {
        $message = (new LineMessage)
            ->withSender(icon: 'icon')
            ->text('test');

        $this->assertSame('icon', $message->toArray()['messages'][0]->getSender()->getIconUrl());
    }

    public function test_quick_reply()
    {
        $message = (new LineMessage)
            ->withQuickReply($quick = new QuickReply(['items' => []]))
            ->text('test');

        $this->assertSame($quick, $message->toArray()['messages'][0]->getQuickReply());
    }

    public function test_flex_bubble()
    {
        $bubble = new FlexBubble(json_decode(file_get_contents(__DIR__.'/./Fixtures/flex-simulator/bubble.json'),
            true));

        $flex = (new FlexMessage)
            ->setType(MessageType::FLEX)
            ->setContents($bubble);

        $message = (new LineMessage)
            ->message($flex);

        $this->assertSame('bubble', $message->toArray()['messages'][0]->getContents()->getType());
    }

    public function test_flex_carousel()
    {
        $carousel = new FlexCarousel(json_decode(file_get_contents(__DIR__.'/./Fixtures/flex-simulator/carousel.json'),
            true));

        $flex = (new FlexMessage)
            ->setType(MessageType::FLEX)
            ->setContents($carousel);

        $message = (new LineMessage)
            ->message($flex);

        $this->assertSame('carousel', $message->toArray()['messages'][0]->getContents()->getType());
    }

    public function test_text_v2()
    {
        $text_v2 = new TextMessageV2;
        $text_v2->setType('textV2');

        $user_target = new UserMentionTarget;
        $user_target->setType(MentioneeType::TYPE_USER);
        $user_target->setUserId('test');
        // dump($user_target->__toString());

        $user = new MentionSubstitutionObject;
        $user->setType('mention');
        $user->setMentionee($user_target);
        // dump($user->__toString());

        $emoji = new EmojiSubstitutionObject;
        $emoji->setType('emoji');
        $emoji->setProductId('p');
        $emoji->setEmojiId('e');

        $everyone_target = new AllMentionTarget;
        $everyone_target->setType(MentioneeType::TYPE_ALL);

        $everyone = new MentionSubstitutionObject;
        $everyone->setType('mention');
        $everyone->setMentionee($everyone_target);

        $text_v2->setSubstitution([
            'user' => $user,
            'emoji' => $emoji,
            'everyone' => $everyone,
        ]);

        $text_v2->setText('{user} {emoji} {everyone}');

        // dump($text_v2->__toString());

        $message = (new LineMessage)
            ->message($text_v2);

        // dump($message->toArray());

        $this->assertSame('textV2', $message->toArray()['messages'][0]->getType());
    }
}
