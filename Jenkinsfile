pipeline {
  agent any
  stages {
    stage('Composer') {
      steps {
        sh 'composer install'
      }
    }
    stage('Install Assetics') {
      steps {
        sh '''
            php app/console assetic:dump
        '''
      }
    }
    stage('Set Ref to master') {
      steps {
        sh 'git fetch --no-tags https://github.com/ammartins/bankcree +refs/heads/master:refs/remotes/origin/master'
      }
    }
    stage('Lint') {
      steps {
        sh '''
            phplint \'**/*.php\' \'!vendor/**\' \'!app/cache/**\'
        '''
      }
    }
    stage('PHPCS') {
      steps {
        sh '''
            # vendor/squizlabs/php_codesniffer/bin/phpcs src
        '''
      }
    }
    stage('Merge Dev To Master') {
      steps {
        sh '''
                cd /tmp &&
                git clone https://github.com/ammartins/bankcree &&
                cd bankcree/ &&
                git pull origin dev &&
                git status &&
                rm -rf /tmp/bankcree/
            '''
      }
    }
  }
}
