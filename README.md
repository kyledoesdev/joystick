[![Tests](https://github.com/kyledoesdev/joystick/actions/workflows/tests.yml/badge.svg)](https://github.com/kyledoesdev/joystick/actions/workflows/tests.yml)

# Joystick Jury 🕹️

Joystick Jury is an app I built for my friends to give us a solution to figuring out what game to pla on our video game night.
The app allows you to log in with your twitch account, join your friends' group & create game suggesstions for a feed (game session) and
vote on the other suggestions.

It also includes discord webhook intergration to send alerts to the discord server that is used for voice chat for game night.

## Current Features 🚀
- Groups
    - Discord Webhook integration per group w/ toggleable settings for determining when an action is performed, if discord pings should be sent.
    - Invite System with email & in app notifications for group invitations for both the invitee & group owner
    - A group "backlog" is automatically created
- Feeds
    - Feeds represent a game night or session and have a start time that is casted to the users' timezone.
    - Feeds can be created by any member in the group if the group owner allows for that.
- Suggestions
    - A group member can submit suggestions into a feed for a game.
    - The suggestion adding features hooks into the Twitch API using the user's twitch token to search the /categories API endpoint to handle game names, assets, etc.
    - Auto Votes - when a user adds a suggestion, it auto adds their "Up Vote" for them
- Votes
    - Users can Up Vote, Down Vote or neutral vote on every game suggestion.
    - Users can view a table of all of the votes for a suggestion via a table and hover tooltip

## Upcoming Features 👨‍💻
- Copy a Suggestion from one feed to another feed. (Copy from backlog to a specific game night feed)
- Group Moderators or admins that have elevated permissions when handling group submissions
- Group member list on feeds page.
- Reminder ping to remind a user to vote on a feed's game night suggestions.
- User prefs & notification disabling for emails.
- In app notification system for alerting when a discord ping fails, a game night is approaching & more.

Stay tuned to the [project board](https://github.com/users/kyledoesdev/projects/1/views/1) for what features, bugs & issues are being worked on.

### TODO

Contributing Guide & Installation Guide
