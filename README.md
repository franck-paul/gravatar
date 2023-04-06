# gravatar

[![Release](https://img.shields.io/github/v/release/franck-paul/gravatar)](https://github.com/franck-paul/gravatar/releases)
[![Date](https://img.shields.io/github/release-date/franck-paul/gravatar)](https://github.com/franck-paul/gravatar/releases)
[![Issues](https://img.shields.io/github/issues/franck-paul/gravatar)](https://github.com/franck-paul/gravatar/issues)
[![Dotclear](https://img.shields.io/badge/dotclear-v2.24-blue.svg)](https://fr.dotclear.org/download)
[![Dotaddict](https://img.shields.io/badge/dotaddict-official-green.svg)](https://plugins.dotaddict.org/dc2/details/gravatar)
[![License](https://img.shields.io/github/license/franck-paul/gravatar)](https://github.com/franck-paul/gravatar/blob/master/LICENSE)

## Description

Ce plugin se charge d’afficher les Gravatars des auteurs des billets et/ou des commentaires sur votre blog.

Si arrivé ici vous ne savez pas de quoi je parle, passez votre chemin ou allez jeter un œil sur le site [Gravatar](http://fr.gravatar.com/) où vous trouverez toutes les informations requises. Cela dit, comme je suis de bonne humeur, je précise simplement qu’il s’agit d’afficher une image qui sert d’avatar, image reliée à votre adresse Email que vous utilisez sur le blog ou pour commenter sur les blogs des copains.

## Utilisation

Une fois installé et activé (voir le menu **Blog**), il faut que vous indiquiez où doivent être affichées ces images. En regard des auteurs de billets, et/ou en regard des auteurs des commentaires.

Le plugin se charge d’insérer **automatiquement** cette image juste après le nom de l’auteur. Vous n’avez aucunement besoin de modifier le thème que vous utilisez et ce plugin reste actif y compris en cas de changement de thème.

Quelques options sont à votre disposition pour spécifier, si nécessaire, la taille des images (carrées) insérées (80 pixels par défaut), le type d’image par défaut affichée si l’email (de l’auteur) ne correspond à aucun compte chez Gravatar, ainsi que le classement minimum que vous souhaitez pour ces images. Gravatar classe les images enregistrées selon 4 catégories (définition reprise sur le site Gravatar) :

* **G** : Un Gravatar classé ‘G’ convient à tout type de site, et à toute audience.
* **PG** : Un Gravatar classé ‘PG’ peut contenir des jurons, ou des images potentiellement choquantes représentant de la violence légère ou des individus habillés de manière provocante.
* **R** : Un Gravatar classé ‘R’ comprend des images choquantes représentant de la violence intense, de la nudité, ou de l’emploi de drogues dures.
* **X** : Un Gravatar classé ‘X’ comprend des images extrêmes ou dérangeantes à cause des représentations crues de nature sexuelle, ou de la violence extrême.

Si, par exemple, vous ne voulez pas d’images portant des classements **R** et **X**, spécifiez alors le classement **PG**. Chaque classement inclus les images des précédents dans la liste ci-dessus.

## Styles

Par défaut l’image est affichée dans une balise `<img … />` possédant une classe `gravatar`. Vous pouvez utiliser le champ Style CSS pour les images Gravatars où vous pouvez spécifier le style à appliquer à cette classe.

## Développement

Vous avez, en plus du système automatique, la possibilité d’utiliser deux balises templates, `{{tpl:EntryAuthorGravatar}}` et `{{tpl:CommentAuthorGravatar}}` respectivement utilisées pour insérer le gravatar de l’auteur du billet et celui de l’auteur du commentaire.

Si vous utilisez l’une ou l’autre de ces deux balises dans vos fichiers *template*, n’oubliez-pas de désactiver l’insertion automatique correspondante sinon vous vous retrouveriez avec deux fois la même image.

Comme pour les images insérées automatiquement la classe `gravatar` est appliquée lors de l’utilisation des balises.
