

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `user` text NOT NULL,
  `pass` text NOT NULL,
  `permissions` int(11) NOT NULL COMMENT '1是管理员'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO `admin` (`id`, `user`, `pass`, `permissions`) VALUES
(1, 'admin', 'admin', 1);



CREATE TABLE `bd_user` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL COMMENT '名称',
  `cookie` text NOT NULL COMMENT '身份值',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `use` datetime NOT NULL COMMENT '时间表示最后一次使用日期',
  `state` tinyint(4) NOT NULL COMMENT '状态 0能用，-1死亡，-2是待测试',
  `switch` tinyint(11) NOT NULL COMMENT '开关 0开 -1关',
  `vip_type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `user_agent` text NOT NULL,
  `cookie` text NOT NULL COMMENT '获取列表的cookie',
  `AnnounceSwitch` tinyint(1) NOT NULL COMMENT '公告开关，1开0关',
  `Announce` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `config` (`id`, `title`, `user_agent`, `cookie`, `AnnounceSwitch`, `Announce`) VALUES
(1, '就是加速', 'netpan', '', 1, '就是加速');

ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `bd_user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `bd_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- 使用表AUTO_INCREMENT `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;
