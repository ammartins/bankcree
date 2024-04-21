[![Codacy Badge](https://api.codacy.com/project/badge/Grade/f95e8f0e24d94b3992436996108e6bb6)](https://www.codacy.com/app/ChiliConGraphics/abnamro?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ammartins/abnamro&amp;utm_campaign=Badge_Grade)

# Build Image
	docker build --pull -t bankcree -f Dockerfile .
# Push Image
	docker tag bankcree ammartins/abnapp:<TAG> && docker push ammartins/abnapp:<TAG>

### Folder Path
app-data/2020/TAB/1