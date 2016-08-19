GitHub API
======

Account
=====

username: spIIIctre
password: sp3:-)ctre

API Tests
------------

curl -i -u "spIIIctre:sp3:-)ctre" https://api.github.com/users/spIIIctre
curl -i -u spIIIctre https://api.github.com/user
curl -i -u spIIIctre -d '{"scopes": ["repo", "user"], "note": "getting-started"}' https://api.github.com/authorizations

curl -i -u spIIIctre:sp8ctre https://api.github.com/user

Token
--------
9075afcec07a292958baf366faa4e6d30aacbc70

Basic
-------
curl -i -u "lauthiamkok:h@me627039" https://api.github.com/users/lautiamkok

curl -u lauthiamkok:9075afcec07a292958baf366faa4e6d30aacbc70 https://api.github.com/users/lautiamkok

Get a use GitHub profile
-----------------------------------
curl https://api.github.com/users/lautiamkok

Response with headers using -i flag:
curl -i https://api.github.com/users/lautiamkok

Authentication
---------------------
curl -i -u "lauthiamkok:h@me627039" https://api.github.com/users/lautiamkok

Get your own user profile
-------------------------------------
curl -i -u "lauthiamkok:9075afcec07a292958baf366faa4e6d30aacbc70" https://api.github.com/user

Or (with header auth):

curl -i -H 'Authorization: token 9075afcec07a292958baf366faa4e6d30aacbc70' https://api.github.com/user

Get a repo
---------------
curl -i https://api.github.com/repos/lautiamkok/js-node

Get all repos
---------------
users/:username/repos

curl -i https://api.github.com/users/lautiamkok/repos

Get all repos
------------------
 (with header auth)
curl -i -H 'Authorization: token 9075afcec07a292958baf366faa4e6d30aacbc70' https://api.github.com/user/repos

Create a repo
--------------------
 (with header auth)
curl -XPOST -H 'Authorization: token 9075afcec07a292958baf366faa4e6d30aacbc70' https://api.github.com/user/repos -d '{"name":"my-new-repo","description":"my new repo description"}'

Or:
curl -i -H 'Authorization: token 9075afcec07a292958baf366faa4e6d30aacbc70' https://api.github.com/user/repos -d '{"name":"my-new-repo2","description":"my new repo description"}'

Or:
curl -i -H 'Authorization: token 9075afcec07a292958baf366faa4e6d30aacbc70' -d '{"name": "blog", "auto_init": "true","false": "true","gitignore_template": "nanoc"}' https://api.github.com/user/repos

Or (with auth):
curl -i -u "lauthiamkok:9075afcec07a292958baf366faa4e6d30aacbc70" https://api.github.com/user/repos -d '{"name":"my-new-repo","description":"my new repo description"}'

Now retreive:
curl -i https://api.github.com/repos/lautiamkok/<repo-name>

List pull requests
-------------------------
GET /repos/:owner/:repo/pulls

curl -i https://api.github.com/repos/jquery/jquery/pulls

Get a single pull request
------------------------------------

GET /repos/:owner/:repo/pulls/:number

curl -i https://api.github.com/repos/jquery/jquery/pulls/1051

List commits on a pull request
--------------------------------------------

GET /repos/:owner/:repo/pulls/:number/commits

curl -i https://api.github.com/repos/jquery/jquery/pulls/1051/commits

Emojis
---------

GET /emojis

curl -i https://api.github.com/emojis