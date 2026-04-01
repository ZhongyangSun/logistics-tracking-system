INSERT INTO users (username, password_hash, role)
VALUES
('sys_user', '$2y$12$Fl7Yo3RoQdQM7UMd2wBRnOmsDNvuYuBLitdl05.z/of/A9fuBe.ye', 'admin')
ON CONFLICT (username) DO NOTHING;

INSERT INTO shipments (tracking_number, sender_name, receiver_name, origin, destination, current_status\)
VALUES
('TRK100001', 'Cao Cao', 'Zhuge Liang', 'Luoyang', 'Chibi', 'Pending',),
('TRK100002', 'Cao Cao', 'Sima Yi', 'Xuchang', 'Luoyang', 'In Transit',),
('TRK100003', 'Sun Quan', 'Zhou Yu', 'Jianye', 'Jiangxia', 'Delivered',)
ON CONFLICT (tracking_number) DO NOTHING;