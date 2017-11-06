
--
-- Structure de la table `um_recovery`
--

CREATE TABLE `um_recovery` (
  `id` int(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `user` varchar(100) NOT NULL,
  `expire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `um_session`
--

CREATE TABLE `um_session` (
  `id` int(255) NOT NULL,
  `user` varchar(100) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `expire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `um_user`
--

CREATE TABLE `um_user` (
  `id` int(255) NOT NULL,
  `user` varchar(100) NOT NULL,
  `pass` longtext NOT NULL,
  `email` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `adresse` longtext NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `um_recovery`
--
ALTER TABLE `um_recovery`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `um_session`
--
ALTER TABLE `um_session`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `um_user`
--
ALTER TABLE `um_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `um_recovery`
--
ALTER TABLE `um_recovery`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT pour la table `um_session`
--
ALTER TABLE `um_session`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT pour la table `um_user`
--
ALTER TABLE `um_user`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

COMMIT;
