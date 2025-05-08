CREATE SCHEMA IF NOT EXISTS lbaw24155;
SET search_path TO lbaw24155;

DROP TYPE IF EXISTS user_notification_type CASCADE;
DROP TYPE IF EXISTS group_notification_types CASCADE;
DROP TYPE IF EXISTS post_notification_types CASCADE;

DROP TABLE IF EXISTS post_tag CASCADE;
DROP TABLE IF EXISTS post_likes CASCADE;
DROP TABLE IF EXISTS group_members CASCADE;
DROP TABLE IF EXISTS flick_post CASCADE;
DROP TABLE IF EXISTS block_friend CASCADE;
DROP TABLE IF EXISTS share_post CASCADE;
DROP TABLE IF EXISTS friend CASCADE;
DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS message_notification CASCADE;
DROP TABLE IF EXISTS group_notification CASCADE;
DROP TABLE IF EXISTS user_notification CASCADE;
DROP TABLE IF EXISTS post_notification CASCADE;
DROP TABLE IF EXISTS friend_request CASCADE;
DROP TABLE IF EXISTS banned_user CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS tag CASCADE;
DROP TABLE IF EXISTS groups CASCADE;
DROP TABLE IF EXISTS post CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS deleted_users CASCADE;
DROP TABLE IF EXISTS blocked_users CASCADE;
DROP TABLE IF EXISTS group_invite CASCADE;
DROP TABLE IF EXISTS react_comment CASCADE;
DROP TABLE IF EXISTS comment_like CASCADE;
DROP TABLE IF EXISTS post_media CASCADE;

DO $$ BEGIN
    CREATE TYPE user_notification_type AS ENUM ('FRIEND REQUEST', 'REQUEST ACCEPTED');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE group_notification_types AS ENUM ('JOIN GROUP', 'LEAVE GROUP', 'INVITED TO GROUP', 'GROUP MESSAGE');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE post_notification_types AS ENUM ('POST LIKE', 'POST COMMENT', 'POST SHARE', 'COMMENT LIKE', 'POST FLICK', 'COMMENT REPLY', 'COMMENT REACT');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- Define tables
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    age INT NOT NULL,
    profile_picture TEXT DEFAULT '/images/DefaultProfile.png'
    );

CREATE TABLE deleted_users (
    user_id INT PRIMARY KEY,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blocked_users (
    user_id INT PRIMARY KEY,
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE groups (
    id SERIAL PRIMARY KEY,
    owner_id INTEGER REFERENCES users(id),
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE post (
    id SERIAL PRIMARY KEY,
    author_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    date TIMESTAMP NOT NULL,
    is_public BOOLEAN DEFAULT TRUE,
    title VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    like_num INT DEFAULT 0 CHECK (like_num >= 0),
    comment_num INT DEFAULT 0 CHECK (like_num >= 0),
    flick_num INT DEFAULT 0 CHECK (flick_num >= 0),
    share_num INT DEFAULT 0 CHECK (share_num >= 0)
);

CREATE TABLE post_media (
    id SERIAL PRIMARY KEY,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    file_path TEXT NOT NULL,
    file_type VARCHAR(50) NOT NULL CHECK (file_type IN ('image', 'video')),
    "order" INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE report (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,  
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,  
    type VARCHAR(50) NOT NULL, 
    date TIMESTAMP NOT NULL  
);

CREATE TABLE comment (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    parent_id INT DEFAULT NULL REFERENCES comment(id) ON DELETE CASCADE,
    like_num INT DEFAULT 0 CHECK (like_num >= 0),
    reply_num INT DEFAULT 0 CHECK (like_num >= 0),
    content TEXT NOT NULL,
    edited BOOLEAN DEFAULT FALSE,
    date TIMESTAMP NOT NULL
);

CREATE TABLE admin (
    user_id INT NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,  
    is_super BOOLEAN DEFAULT FALSE  
);

CREATE TABLE message (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    date TIMESTAMP NOT NULL,
    sender_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    receiver_id INT REFERENCES users(id) ON DELETE CASCADE,
    group_id INT REFERENCES groups(id) ON DELETE CASCADE,
    post_id INT REFERENCES post(id) ON DELETE CASCADE  
);
CREATE TABLE tag (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
    sender_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    receiver_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    date TIMESTAMP NOT NULL,
    seen BOOLEAN DEFAULT FALSE
);

CREATE TABLE banned_user (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    admin_id INT NOT NULL REFERENCES admin(user_id) ON DELETE CASCADE
);

CREATE TABLE message_notification (
    notification_id INT NOT NULL REFERENCES notifications(id) ON DELETE CASCADE,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE  
);

CREATE TABLE group_notification (
    notification_id INT NOT NULL REFERENCES notifications(id) ON DELETE CASCADE,
    group_id INT NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    notification_type group_notification_types NOT NULL
);

CREATE TABLE user_notification (
    notification_id INT NOT NULL REFERENCES notifications(id) ON DELETE CASCADE,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    notification_type user_notification_type NOT NULL
);

CREATE TABLE post_notification (
    notification_id INT NOT NULL REFERENCES notifications(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    notification_type post_notification_types NOT NULL
);

CREATE TABLE friend_request (
    req_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    rcv_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (req_id, rcv_id)
);

CREATE TABLE post_likes (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, post_id)
);

CREATE TABLE post_tag (
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    tag_id INT NOT NULL REFERENCES tag(id) ON DELETE CASCADE,
    PRIMARY KEY (post_id, tag_id)
);

CREATE TABLE group_members (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    group_id INT NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, group_id)
);

CREATE TABLE flick_post (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, post_id)
);

CREATE TABLE block_friend (
    blocker_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    blocked_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE, 
    PRIMARY KEY (blocker_id, blocked_id)
);

CREATE TABLE share_post (
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (post_id, user_id)
);

CREATE TABLE friend (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    friend_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, friend_id)
);

CREATE TABLE react_comment (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    comment_id INT NOT NULL REFERENCES comment(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, comment_id) 
);

CREATE TABLE group_invite (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,  
    group_id INT NOT NULL REFERENCES groups(id) ON DELETE CASCADE, 
    PRIMARY KEY (user_id, group_id)  
);

CREATE TABLE comment_like (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,  
    comment_id INT NOT NULL REFERENCES comment(id) ON DELETE CASCADE, 
    PRIMARY KEY (user_id, comment_id)  
);

-- Indexes
CREATE INDEX notification_date_index ON notifications USING hash(receiver_id);
CREATE INDEX idx_author_id_date ON post (author_id, date);
CREATE INDEX idx_comments_post_date ON comment (post_id, date);
CLUSTER comment USING idx_comments_post_date;

-- Full-Text Search
ALTER TABLE post ADD COLUMN tsvectors TSVECTOR;

CREATE OR REPLACE FUNCTION post_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('portuguese', NEW.title), 'A') || 
            setweight(to_tsvector('portuguese', NEW.description), 'B')  
        );
    ELSIF TG_OP = 'UPDATE' THEN
        IF (NEW.title <> OLD.title OR NEW.description <> OLD.description) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('portuguese', NEW.title), 'A') ||
                setweight(to_tsvector('portuguese', NEW.description), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$ LANGUAGE plpgsql;

CREATE TRIGGER post_search_update
BEFORE INSERT OR UPDATE ON post
FOR EACH ROW
EXECUTE FUNCTION post_search_update();

CREATE INDEX search_post ON post USING GIN (tsvectors);

-------------------------------------------------- Triggers ----------------------------------------------------
-- Trigger Function for updating like_num
CREATE OR REPLACE FUNCTION update_like_num()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        UPDATE comment
        SET like_num = like_num + 1
        WHERE id = NEW.comment_id;
    ELSIF TG_OP = 'DELETE' THEN
        UPDATE comment
        SET like_num = like_num - 1
        WHERE id = OLD.comment_id;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER update_like_num_trigger
AFTER INSERT OR DELETE ON comment_like
FOR EACH ROW
EXECUTE FUNCTION update_like_num();


-- Trigger Function for updating reply_num
CREATE OR REPLACE FUNCTION update_reply_num()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' AND NEW.parent_id IS NOT NULL THEN
        UPDATE comment
        SET reply_num = reply_num + 1
        WHERE id = NEW.parent_id;
    ELSIF TG_OP = 'DELETE' AND OLD.parent_id IS NOT NULL THEN
        UPDATE comment
        SET reply_num = reply_num - 1
        WHERE id = OLD.parent_id;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_reply_num_trigger
AFTER INSERT OR DELETE ON comment
FOR EACH ROW
EXECUTE FUNCTION update_reply_num();


-- A user can not flick a post more than one time
CREATE OR REPLACE FUNCTION prevent_duplicate_post_flicks() 
RETURNS TRIGGER AS $$ 
BEGIN 
    IF EXISTS ( 
        SELECT 1 FROM flick_post 
        WHERE user_id = NEW.user_id AND post_id = NEW.post_id  
    ) THEN 
        RAISE EXCEPTION 'Duplicate flick: You have already flicked this post.'; 
    END IF; 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER prevent_duplicate_post_flicks
BEFORE INSERT ON flick_post
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_post_flicks();


-- A user can not send a friend request to himself
CREATE OR REPLACE FUNCTION prevent_self_friend_request() 
RETURNS TRIGGER AS $$ 
BEGIN 
    IF NEW.req_id = NEW.rcv_id THEN 
        RAISE EXCEPTION 'Invalid friend request: You cannot send a friend request to yourself.'; 
    END IF; 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER prevent_self_friend_request
BEFORE INSERT ON friend_request
FOR EACH ROW
EXECUTE FUNCTION prevent_self_friend_request();


-- A user can only send friend requests to non-friends
CREATE OR REPLACE FUNCTION prevent_request_to_existing_friend() 
RETURNS TRIGGER AS $$ 
BEGIN 
    IF EXISTS (  
        SELECT 1 FROM friend 
        WHERE (user_id = NEW.req_id AND friend_id = NEW.rcv_id) OR 
              (user_id = NEW.rcv_id AND friend_id = NEW.req_id)  
    ) THEN  
        RAISE EXCEPTION 'Cannot send friend request: You are already friends with this user.'; 
    END IF; 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER prevent_request_to_existing_friend
BEFORE INSERT ON friend_request
FOR EACH ROW EXECUTE FUNCTION prevent_request_to_existing_friend();


-- Messages can be sent to a user or a group, never both
CREATE OR REPLACE FUNCTION enforce_message_recipient() 
RETURNS TRIGGER AS $$ 
BEGIN 
    IF (NEW.receiver_id IS NOT NULL AND NEW.group_id IS NOT NULL) OR  
        (NEW.receiver_id IS NULL AND NEW.group_id IS NULL) THEN 
        RAISE EXCEPTION 'Message must be sent to either a user or a group, but not both.'; 
    END IF; 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER enforce_message_recipient
BEFORE INSERT ON message
FOR EACH ROW
EXECUTE FUNCTION enforce_message_recipient();


-- Restricting duplicate likes on a post
CREATE OR REPLACE FUNCTION prevent_duplicate_post_likes() 
RETURNS TRIGGER AS $$ 
BEGIN 
    IF EXISTS ( 
        SELECT 1 FROM post_likes 
        WHERE user_id = NEW.user_id AND post_id = NEW.post_id 
    ) THEN 
        RAISE EXCEPTION 'Duplicate like: You have already liked this post.';  
    END IF; 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER prevent_duplicate_post_likes
BEFORE INSERT ON post_likes
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_post_likes();


-- Restricting duplicate friend requests
CREATE OR REPLACE FUNCTION prevent_duplicate_friend_requests() 
RETURNS TRIGGER AS $$ 
BEGIN 
    IF EXISTS ( 
        SELECT 1 FROM friend_request 
        WHERE req_id = NEW.req_id AND rcv_id = NEW.rcv_id  
    ) THEN 
        RAISE EXCEPTION 'Duplicate friend request: You have already sent a friend request to this user.'; 
    END IF; 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER prevent_duplicate_friend_requests
BEFORE INSERT ON friend_request
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_friend_requests();

-- Delete user trigger
CREATE OR REPLACE FUNCTION before_delete_user() RETURNS TRIGGER AS $$
BEGIN
    PERFORM delete_user(OLD.id);
    RETURN NULL; -- Prevent the actual delete
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_before_delete_user
BEFORE DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION before_delete_user();

-- Generate notifications when user receives a group message
CREATE OR REPLACE FUNCTION notify_on_group_message() 
RETURNS TRIGGER AS $$ 
DECLARE 
    member RECORD; 
BEGIN 
    IF NEW.group_id IS NOT NULL THEN 
        FOR member IN SELECT user_id FROM group_members WHERE group_id = NEW.group_id  
        LOOP 
            INSERT INTO notifications (sender_id, receiver_id, content, date, seen)
            VALUES (NEW.sender_id, member.user_id, NEW.content, CURRENT_DATE, FALSE); 
        INSERT INTO group_notification (notification_id, group_id, notification_type) 
        VALUES ( 
            (SELECT currval(pg_get_serial_sequence('notifications', 'id'))), 
            NEW.group_id, 
            'GROUP MESSAGE' 
        ); 
    END LOOP; 
END IF; 
RETURN NEW; 

END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER notify_on_group_message
AFTER INSERT ON message
FOR EACH ROW WHEN (NEW.group_id IS NOT NULL)
EXECUTE FUNCTION notify_on_group_message();


-- Generate notifications when a user’s comment is liked
CREATE OR REPLACE FUNCTION notify_on_comment_like() 
RETURNS TRIGGER AS $$ 
BEGIN  
    INSERT INTO notifications (sender_id, receiver_id, content, date, seen) 
    VALUES (NEW.user_id, (SELECT user_id FROM comment WHERE id = NEW.comment_id), 'Your comment was liked.', CURRENT_DATE, FALSE); 
INSERT INTO post_notification (notification_id, post_id, notification_type) 
VALUES (  
    (SELECT currval(pg_get_serial_sequence('notifications', 'id'))), 
    (SELECT post_id FROM comment WHERE id = NEW.comment_id), 
    'COMMENT LIKE'  
);  
RETURN NEW;  

END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER notify_on_comment_like
AFTER INSERT ON comment_like
FOR EACH ROW
EXECUTE FUNCTION notify_on_comment_like();


-- Generate notifications when user is invited to a group
CREATE OR REPLACE FUNCTION notify_on_group_invite() 
RETURNS TRIGGER AS $$ 
BEGIN 
    INSERT INTO notifications (sender_id, receiver_id, content, date, seen) 
    VALUES (NEW.user_id, (SELECT owner_id FROM groups WHERE id = NEW.group_id), 'You have been invited to a group.', CURRENT_DATE, FALSE); 
    INSERT INTO group_notification (notification_id, group_id, notification_type) 
    VALUES ( 
        (SELECT currval(pg_get_serial_sequence('notifications', 'id'))), 
        NEW.group_id, 
        'INVITED TO GROUP' 
    ); 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER notify_on_group_invite
AFTER INSERT ON group_invite
FOR EACH ROW
WHEN (NEW.group_id IS NOT NULL)
EXECUTE FUNCTION notify_on_group_invite();


-- Generate notifications when user receives a friend request
CREATE OR REPLACE FUNCTION notify_on_friend_request() 
RETURNS TRIGGER AS $$ 
BEGIN 
    INSERT INTO notifications (sender_id, receiver_id, content, date, seen) 
    VALUES (NEW.req_id, NEW.rcv_id, 'You have a new friend request from '|| (SELECT name FROM users WHERE id = NEW.req_id), CURRENT_DATE, FALSE); 
    INSERT INTO user_notification (notification_id, user_id, notification_type) 
    VALUES (  
        (SELECT currval(pg_get_serial_sequence('notifications', 'id'))), 
        NEW.rcv_id, 
        'FRIEND REQUEST' 
     ); 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 
CREATE TRIGGER notify_friend_request
AFTER INSERT ON friend_request
FOR EACH ROW
EXECUTE FUNCTION notify_on_friend_request();


-- Automatically update flick_num counts when users flick with a post
CREATE OR REPLACE FUNCTION increment_flick_count() 
RETURNS TRIGGER AS $$ 
BEGIN 
    UPDATE post SET flick_num = flick_num + 1 WHERE id = NEW.post_id;  
    RETURN NEW; 
END;  
$$ LANGUAGE plpgsql;  
CREATE TRIGGER after_flick_insert
AFTER INSERT ON flick_post
FOR EACH ROW
EXECUTE FUNCTION increment_flick_count();


-- Automatically update share_num counts when users share a post
CREATE OR REPLACE FUNCTION increment_share_count() 
RETURNS TRIGGER AS $$ 
BEGIN 
    UPDATE post SET share_num = share_num + 1 WHERE id = NEW.post_id;  
    RETURN NEW; 
END;  
$$ LANGUAGE plpgsql;  
CREATE TRIGGER after_share_insert
AFTER INSERT ON share_post
FOR EACH ROW
EXECUTE FUNCTION increment_share_count();

-- Automatically update like_num counts when users interact with posts
CREATE OR REPLACE FUNCTION increment_like_count() 
RETURNS TRIGGER AS $$ 
BEGIN 
    UPDATE post SET like_num = like_num + 1 WHERE id = NEW.post_id;  
    RETURN NEW; 
END;  
$$ LANGUAGE plpgsql;  
CREATE TRIGGER after_like_insert
AFTER INSERT ON post_likes
FOR EACH ROW
EXECUTE FUNCTION increment_like_count();

-- Automatically update comment_num counts when users interact with posts
CREATE OR REPLACE FUNCTION increment_comment_count() 
RETURNS TRIGGER AS $$ 
BEGIN 
    UPDATE post SET comment_num = comment_num + 1 WHERE id = NEW.post_id;  
    RETURN NEW; 
END;  
$$ LANGUAGE plpgsql;  
CREATE TRIGGER after_comment_insert
AFTER INSERT ON comment
FOR EACH ROW
EXECUTE FUNCTION increment_comment_count();


-- Date of post must be less than current date
CREATE OR REPLACE FUNCTION validate_post_date()  
RETURNS TRIGGER AS $$  
BEGIN  
    IF NEW.date > (CURRENT_TIMESTAMP + INTERVAL '1 minute') THEN 
        RAISE EXCEPTION 'Post date cannot be in the future.'; 
    END IF;
    RETURN NEW; 
END;  
$$ LANGUAGE plpgsql; 
CREATE TRIGGER validate_post_date_on_insert
BEFORE INSERT ON post
FOR EACH ROW
EXECUTE FUNCTION validate_post_date();


-- Loggers must be over 13 years old
CREATE OR REPLACE FUNCTION enforce_age_requirement()
RETURNS TRIGGER AS $$  
BEGIN  
    IF NEW.age < 13 THEN 
        RAISE EXCEPTION 'Users must be at least 13 years old to register.';  
    END IF;  
    RETURN NEW;  
END;  
$$ LANGUAGE plpgsql;  
CREATE TRIGGER enforce_age_on_insert
BEFORE INSERT ON users
FOR EACH ROW
EXECUTE FUNCTION enforce_age_requirement();


-- Notify on message received
CREATE OR REPLACE FUNCTION notify_on_message_received() 
RETURNS TRIGGER AS $$ 
BEGIN 
    INSERT INTO notifications (sender_id, receiver_id, content, date, seen) 
    VALUES (NEW.sender_id, NEW.receiver_id, 'You have a new message.', CURRENT_DATE, FALSE); 
    INSERT INTO message_notification (notification_id, user_id) 
    VALUES (  
        (SELECT currval(pg_get_serial_sequence('notifications', 'id'))), 
        NEW.receiver_id
    ); 
    RETURN NEW; 
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER notify_on_message_received
AFTER INSERT ON message
FOR EACH ROW
WHEN (NEW.receiver_id IS NOT NULL)
EXECUTE FUNCTION notify_on_message_received();

-- Notify on group join
CREATE OR REPLACE FUNCTION notify_on_group_join() 
RETURNS TRIGGER AS $$ 
BEGIN 
    INSERT INTO notifications (sender_id, receiver_id, content, date, seen) 
    VALUES (NEW.user_id, (SELECT owner_id FROM groups WHERE id = NEW.group_id), 'User has joined your group.', CURRENT_DATE, FALSE); 
    INSERT INTO group_notification (notification_id, group_id, notification_type) 
    VALUES (  
        (SELECT currval(pg_get_serial_sequence('notifications', 'id'))),  
        NEW.group_id,  
        'JOIN GROUP' 
    );  
    RETURN NEW; 
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER notify_group_join
AFTER INSERT ON group_members
FOR EACH ROW
EXECUTE FUNCTION notify_on_group_join();

-- Generate notifications for post authors when their posts receive likes, comments, shares, or flicks
CREATE OR REPLACE FUNCTION create_interaction_notification() 
RETURNS TRIGGER AS $$ 
DECLARE 
    notification_type TEXT;
    notification_message TEXT;
    post_title TEXT;
    user_name TEXT;
BEGIN 
    SELECT title INTO post_title FROM post WHERE id = NEW.post_id;
    SELECT name INTO user_name FROM users WHERE id = NEW.user_id;
    
    IF TG_TABLE_NAME = 'post_likes' THEN 
        notification_type := 'POST LIKE'; 
        notification_message := 'Your post "' || post_title || '" received a new like from ' || user_name || '.';
    ELSIF TG_TABLE_NAME = 'comment' THEN 
        notification_type := 'POST COMMENT'; 
        notification_message := 'Your post "' || post_title || '" received a new comment from ' || user_name || '.';
    ELSIF TG_TABLE_NAME = 'share_post' THEN 
        notification_type := 'POST SHARE'; 
        notification_message := 'Your post "' || post_title || '" was shared by ' || user_name || '.';
    ELSIF TG_TABLE_NAME = 'flick_post' THEN 
        notification_type := 'POST FLICK'; 
        notification_message := 'Your post "' || post_title || '" received a new flick from ' || user_name || '.';
    ELSE 
        RAISE EXCEPTION 'Unsupported notification type for table %', TG_TABLE_NAME; 
    END IF;  
    
    INSERT INTO notifications (sender_id, receiver_id, content, date, seen) 
    VALUES (NEW.user_id, (SELECT author_id FROM post WHERE id = NEW.post_id), notification_message, CURRENT_DATE, FALSE);  
    
    INSERT INTO post_notification (notification_id, post_id, notification_type) 
    VALUES (  
        (SELECT currval(pg_get_serial_sequence('notifications', 'id'))),  
        NEW.post_id,  
        notification_type::post_notification_types 
    );  
     
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 

CREATE TRIGGER notify_on_like
AFTER INSERT ON post_likes
FOR EACH ROW
EXECUTE FUNCTION create_interaction_notification();

CREATE TRIGGER notify_on_comment
AFTER INSERT ON comment
FOR EACH ROW
EXECUTE FUNCTION create_interaction_notification();

CREATE TRIGGER notify_on_share
AFTER INSERT ON share_post
FOR EACH ROW
EXECUTE FUNCTION create_interaction_notification();

CREATE TRIGGER notify_on_flick
AFTER INSERT ON flick_post
FOR EACH ROW
EXECUTE FUNCTION create_interaction_notification();

-- Friend request acceptance notification
CREATE OR REPLACE FUNCTION notify_on_friend_request_accept() 
RETURNS TRIGGER AS $$ 
BEGIN 
    INSERT INTO notifications (sender_id, receiver_id, content, date, seen) 
    VALUES (
        NEW.user_id,
        NEW.friend_id,
        'Friend request accepted.',
        CURRENT_DATE,
        FALSE
    );
    INSERT INTO user_notification (notification_id, user_id, notification_type) 
    VALUES (  
        (SELECT currval(pg_get_serial_sequence('notifications', 'id'))), 
        NEW.user_id,  
        'REQUEST ACCEPTED' 
    ); 
    RETURN NEW; 
END; 
$$ LANGUAGE plpgsql; 

CREATE TRIGGER notify_friend_request_accept
AFTER INSERT ON friend
FOR EACH ROW
EXECUTE FUNCTION notify_on_friend_request_accept();

-- Notify on comment reply
CREATE OR REPLACE FUNCTION notify_on_comment_reply() 
RETURNS TRIGGER AS $$ 
BEGIN 
    IF NEW.parent_id IS NOT NULL THEN 
        INSERT INTO notifications (sender_id, receiver_id, content, date, seen)
        VALUES (NEW.user_id, (SELECT user_id FROM comment WHERE id = NEW.parent_id), 'Your comment received a reply.', CURRENT_DATE, FALSE); 
        INSERT INTO post_notification (notification_id, post_id, notification_type) 
        VALUES ( 
            (SELECT currval(pg_get_serial_sequence('notifications', 'id'))),  
            (SELECT post_id FROM comment WHERE id = NEW.parent_id), 
            'COMMENT REPLY' 
        ); 
    END IF; 
    RETURN NEW; 
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER notify_on_comment_reply
AFTER INSERT ON comment
FOR EACH ROW WHEN (NEW.parent_id IS NOT NULL)
EXECUTE FUNCTION notify_on_comment_reply();

-------------------------------------------------- Functions ----------------------------------------------------
--delete user and anonymization
CREATE OR REPLACE FUNCTION delete_user(user_id INT) RETURNS VOID AS $$
BEGIN
    -- Insert the user ID into the deleted_users table
    INSERT INTO deleted_users (user_id) VALUES (user_id);

    -- Anonymize the user data with unique values
    UPDATE users
    SET 
        name ='anonymous',
        username = 'anonymous_' || user_id || '_' || EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::BIGINT,
        age = 100,
        email = 'anonymous_' || user_id || '_' || EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::BIGINT || '@example.com',
        password = user_id || '_' || EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::BIGINT,
        profile_picture = '/images/DefaultProfile.png'
    WHERE id = user_id;

END;
$$ LANGUAGE plpgsql;

-- Function to remove a user from the admin table
CREATE OR REPLACE FUNCTION remove_user_from_admin(user_id INT) RETURNS VOID AS $$
BEGIN
    DELETE FROM admin WHERE admin.user_id = remove_user_from_admin.user_id;
END;
$$ LANGUAGE plpgsql;

