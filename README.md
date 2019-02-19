# Mibew Telegram plugin
Plugin for Mibew Messenger to send notifications on new chats into Telegram.

# Install

1. Get the archive with the plugin sources. You can download it from the [official site](https://mibew.org/plugins#mibew-telegram) or build the plugin from sources.
2. Untar/unzip the plugin's archive.
3. Put files of the plugins to the `<MIBEW-ROOT>/plugins` folder.
4. Configure the plugin by altering the section ```plugins``` in "`<Mibew root>`/configs/config.yml" file.

If the "plugins" structure looks like `plugins: []` it will become:

```yaml
    plugins:
        "Mibew:Telegram": # Plugin's configurations are described below
            token: "YOUR_BOT_API_TOKEN"
            chat: "YOUR_CHAT"
```
or (if you want to send notifications to multiple chats):

```yaml
    plugins:
        "Mibew:Telegram": # Plugin's configurations are described below
            token: "YOUR_BOT_API_TOKEN"
            chat: ["YOUR_CHAT1", "YOUR_CHAT2"]
```

5. Navigate to `<MIBEW-BASE-URL>/operator/plugin` page and enable the plugin.

## Plugin's configurations

The plugin can be configured with values in "`<Mibew root>`/configs/config.yml"
file.

### config.token

Type: `String`

Telegram bot's auth token.

### config.chat

Type: `String` or `Array`

Chat ID or chat IDs to send notifications to.

# Obtaining auth token and chat id(s)

*The information in this section is based upon [this document](http://bernaerts.dyndns.org/linux/75-debian/351-debian-send-telegram-notification).*

1. Create new bot with the help of **@BotFather** and get auth token. See https://core.telegram.org/bots for details.

The bot will be able to send messages either to:

* account that is in chat with the bot;
* any channel it belongs to.

To send any message the bot will need to use either ID of a user or ID of a channel.

2.1 Determine user ID

To get user ID, one need to send any message to the bot from the Telegram client.
Then call the following URL from any web browser: ```https://api.telegram.org/bot<YOUR_BOT_API_TOKEN>/getUpdates```

The answer will appear as a JSON structure:

```json
{"ok":true,"result":[{"update_id":987654321,"message":{"message_id":2, "from":{"id":<USER_ID>, "first_name":"<USER_FIRST_NAME>", "last_name":"<USER_LAST_NAME>", "username":"<USER_NAME>"},"chat":{"id":<USER_ID>, "first_name":"USER_FIRST_NAME", "last_name":"<USER_LAST_NAME>", "username":"<USER_NAME", "type":"private"},"date":1550617591, "text":"<MESSAGE>"}}]}
 ```

Then it will be possible to use `USER_ID` (a number) in your configuration to send notifications to related user.

2.2. Get channel ID

First, create a channel and keep it public as you need to obtain the channel ID. Give a name `@YourNewChannelName` to your channel.

Next, from your bot thread, go to the bot parameters page. You should see that the bot is in both `Members` and `Administrators` lists.

To get channel ID, you just need to send a message to @YourNewChannelName from any web browser using the bot API key: ```https://api.telegram.org/bot<YOUR_BOT_API_TOKEN>/sendMessage?chat_id=@YourNewChannelName&text=FirstMessage```

The answer will appear as a JSON structure:

```json
{"ok":true, "result":{"message_id":1, "chat":{"id":<CHANNEL_ID>, "title":"Your Channel", "username":"YourNewChannelName", "type":"channel"}, "date":1550617591, "text":"<MESSAGE>"}}
```
 
Then it will be possible to use `CHANNEL_ID` in your configuration to send notifications to the related channel.

And you can now edit your channel and convert its type to private.

# Build from sources

1. Obtain a copy of the repository using `git clone`, download button, or another way.
2. Install [node.js](http://nodejs.org/) and [npm](https://www.npmjs.org/).
3. Install [Gulp](http://gulpjs.com/).
4. Install npm dependencies using `npm install`.
5. Run Gulp to build the sources using `gulp default`.

Finally `.tar.gz` and `.zip` archives of the ready-to-use Plugin will be available in release directory.

# License

[Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
