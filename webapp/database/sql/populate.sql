-- População inicial de dados
-- Popula a tabela de `users`
INSERT INTO users (username, password, name, email, age) VALUES 
    ('user1', 'password1', 'User One', 'user1@example.com', 25),
    ('user2', 'password2', 'User Two', 'user2@example.com', 30),
    ('user3', 'password3', 'User Three', 'user3@example.com', 40),
    ('user4', 'password4', 'User Four', 'user4@example.com', 28);

-- Popula a tabela `groups`
INSERT INTO groups (owner_id, name) VALUES 
    (1, 'Group Alpha'),
    (2, 'Group Beta');

-- Popula a tabela `post`
INSERT INTO post (author_id, content, date, is_public, title, description) VALUES 
    (1, 'First post content', CURRENT_DATE, TRUE, 'First Post', 'This is the first post description'),
    (2, 'Second post content', CURRENT_DATE, FALSE, 'Second Post', 'Description for the second post'),
    (3, 'Third post content', CURRENT_DATE, TRUE, 'Third Post', 'This is a public third post description');

-- Popula a tabela `tag`
INSERT INTO tag (name) VALUES 
    ('Tag1'), ('Tag2'), ('Tag3');

-- Popula a tabela `post_tag`
INSERT INTO post_tag (post_id, tag_id) VALUES 
    (1, 1),
    (1, 2),
    (2, 2),
    (3, 3);

-- Popula a tabela `comment`
INSERT INTO comment (user_id, post_id, content, date) VALUES 
    (2, 1, 'Nice post!', CURRENT_DATE),
    (3, 1, 'Interesting thoughts', CURRENT_DATE),
    (1, 2, 'Thanks for sharing!', CURRENT_DATE);

-- Popula a tabela `friend`
INSERT INTO friend (user_id, friend_id) VALUES 
    (1, 2),
    (2, 3),
    (3, 4);

-- Popula a tabela `friend_request`
INSERT INTO friend_request (req_id, rcv_id) VALUES 
    (4, 1);

-- Popula a tabela `post_likes`
INSERT INTO post_likes (user_id, post_id) VALUES 
    (2, 1),
    (3, 1),
    (4, 1),
    (1, 2);

-- Popula a tabela `flick_post`
INSERT INTO flick_post (user_id, post_id) VALUES 
    (1, 3),
    (2, 3);

-- Popula a tabela `report`
INSERT INTO report (user_id, post_id, type, date) VALUES 
    (3, 1, 'Inappropriate Content', CURRENT_DATE);

-- Popula a tabela `message`
INSERT INTO message (content, date, sender_id, receiver_id) VALUES 
    ('Hello there!', CURRENT_DATE, 1, 2),
    ('How are you?', CURRENT_DATE, 2, 1);

-- Popula a tabela `group_members`
INSERT INTO group_members (user_id, group_id) VALUES 
    (1, 1),
    (2, 1),
    (3, 2),
    (4, 2);

-- Popula a tabela `share_post`
INSERT INTO share_post (post_id, user_id) VALUES 
    (1, 2),
    (3, 1);

-- Popula a tabela `react_comment`
INSERT INTO react_comment (user_id, comment_id) VALUES 
    (1, 1),
    (3, 2);

-- Popula a tabela `group_invite`
INSERT INTO group_invite (user_id, group_id) VALUES 
    (3, 1);

-- Popula a tabela `block_friend`
INSERT INTO block_friend (blocked_id, blocker_id) VALUES 
    (4, 2);

-- Popula a tabela `comment_like`
INSERT INTO comment_like (user_id, comment_id) VALUES 
    (1, 2),
    (2, 1);
