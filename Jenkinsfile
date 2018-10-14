pipeline {
  agent any
  stages {
    stage('Composer') {
      steps {
        sh 'composer install --dev'
      }
    }
    stage('Install Assetics') {
      steps {
        sh '''
            php app/console assetic:dump
        '''
      }
    }
    stage('SonarQube analysis') {
        steps {
          sh "/srv/app-sonar-2.4/sonar-runner-2.4/bin/sonar-runner \
                -Dsonar.projectKey=abn-php-macOS \
                -Dsonar.organization=ammartins-github \
                -Dsonar.sources=src \
                -Dsonar.host.url=https://sonarcloud.io \
                -Dsonar.login=4e22c72ef0f3c3f2c914ff84e33e8f810c923111"
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
            vendor/squizlabs/php_codesniffer/bin/phpcs src
        '''
      }
    }
  }
}
