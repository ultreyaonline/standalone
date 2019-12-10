alias ..="cd .."
alias ...="cd ../.."
alias ....="cd ../../../"
alias .....="cd ../../../../"

# Directory listings
# LS_COLORS='no=01;37:fi=01;37:di=07;96:ln=01;36:pi=01;32:so=01;35:do=01;35:bd=01;33:cd=01;33:ex=01;31:mi=00;05;37:or=00;05;37:'
# -G Add colors to ls
# -l Long format
# -h Short size suffixes (B, K, M, G, P)
# -p Postpend slash to folders
alias ls='ls -G --color '
alias ll='ls -lhap '
alias l='ls -FlAG --color'
alias llm="ls -ahlF --color --group-directories-first "


alias art="php artisan $1 $2 $3 $4 $5 $6"
alias a="php artisan $1 $2 $3 $4 $5 $6"
alias tinker="php artisan tinker"
alias cdo="composer dump-autoload -o"
alias csu="composer self-update"
alias dry="composer update --dry-run"

alias g=git
alias gs="git status"
alias gd="git diff"
alias nah="git reset --hard;git clean -df;"


bind '"\e[A": history-search-backward'
bind '"\e[B": history-search-forward'
#bind '"\e[1;5A":history-search-backward'
#bind '"\e[1;5B":history-search-forward'
bind '"\e[1;5C":forward-word'
bind '"\e[1;5D":backward-word'
