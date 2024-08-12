# Book Swap API

## API documentation

### Return all books - optionally filtered

* **URL**

  /api/books

* **Method:**

  `GET`

* **URL Params**

  **Required:**

  There are no required URL params

  **Optional:**

    - `claimed=0|1` - Either 1 or 0 to get only claimed/unclaimed books
    - `search=string` - A search term
    - `genre=int` - Filter results by genre id

  **Example:**

  `/api/books?search=Peach&claimed=1&genre=1`

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** <br />

```json
{
    "data": [
        {
            "id": 2,
            "title": "foo",
            "author": "Test",
            "image": "https://via.placeholder.com/640x480.png/00cc88?text=iure",
            "genre": {
                "id": 1,
                "name": "Action"
            }
        }
    ],
    "message": "Books successfully retrieved"
}
```

* **Error Response:**

    * **Code:** 422 UNPROCESSABLE CONTENT <br />
      **Content:**
        ```json
        {
            "message": "The claimed field must be a number. (and 2 more errors)",
            "errors": {
                "claimed": [
                    "The claimed field must be a number."
                ],
                "genre": [
                    "The selected genre is invalid."
                ],
                "search": [
                    "The search field must be a string."
                ]
            }
        }
        ```

### Return a specific book

* **URL**

  /api/books/{id}

* **Method:**

  `GET`

* **URL Params**

  **Required:**

  There are no required URL params

  **Optional:**

  There are no optional URL params

  **Example:**

  `/api/books/1`

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** <br />

```json
{
    "data": {
        "id": 1,
        "title": "Test",
        "author": "Test person",
        "blurb": "blurb",
        "claimed_by_name": "Ash",
        "image": "https://example.com/image.jpg",
        "page_count": 1000,
        "year": 1980,
        "genre": {
            "id": 1,
            "name": "Action"
        },
        "reviews": [
            {
                "id": 3,
                "name": "Ash",
                "rating": 1,
                "review": "bad"
            }
        ]
    },
    "message": "Book successfully found"
}
```

* **Error Response:**

    * **Code:** 404 NOT FOUND <br />
      **Content:** `{"message": "Book with id 999 not found"}`

### Claim book

* **URL**

  /books/claim/{id}

* **Method:**

  `PUT`

* **Body Data**

  Must be sent as JSON with the correct headers

  **Required:**

    ```json
    {
      "email": "String",
      "name": "string"
    }
    ```

  **Optional:**

  There are no optional body parameters

  **Example:**

  `/api/books/claim/1`

* **Success Response:**

    * **Code:** 200 OK <br />
      **Content:** <br />

  ```json
  {"message": "Book 1 was claimed"}
  ```

* **Error Response:**

    * **Code:** 404 NOT FOUND <br />
      **Content:** `{"message": "Book 10 was not found"}`

    * **Code:** 400 BAD REQUEST <br />
      **Content:** `{"message": "Book 10 is already claimed"}`

    * **Code:** 422 UNPROCESSABLE CONTENT <br />
      **Content:**
    ```json
    {
        "message": "The email field is required. (and 1 more error)",
        "errors": {
            "email": [
                "The email field is required."
            ],
            "name": [
                "The name field is required."
            ]
        }
    }
    ```


### Return book

* **URL**

  /books/return/{id}

* **Method:**

  `PUT`

* **URL Params**

  **Required:**

  There are no required URL params

  **Optional:**

  There are no optional URL params

* **Body Data**

  Must be sent as JSON with the correct headers

  **Required:**

    ```json
    {
      "email": "String",
    }
    ```

  **Optional:**

  There are no optional body parameters

  **Example:**

  `/api/books/return/1`

* **Success Response:**

    * **Code:** 200 OK <br />
      **Content:** <br />

  ```json
  {"message": "Book 1 was returned"}
  ```

* **Error Response:**

    * **Code:** 404 NOT FOUND <br />
      **Content:** `{"message": "Book 10 was not found"}`

    * **Code:** 400 BAD REQUEST <br />
      **Content:** `{"message": "Book 10 is not currently claimed"}`

    * **Code:** 400 BAD REQUEST <br />
      **Content:** `{"message": "Book 1 was not returned. test@test.com did not claim this book."}`

    * **Code:** 422 UNPROCESSABLE CONTENT <br />
      **Content:**
    ```json
    {
        "message": "The email field is required.",
        "errors": {
            "email": [
                "The email field is required."
            ]
        }
    }
    ```
    * **Code:** 500 INTERNAL SERVER ERROR <br />
      **Content:** `{"message": "Book 10 was not able to be returned"}`

### Add a new book

* **URL**

  /api/books

* **Method:**

  `POST`

* **URL Params**

  **Required:**

  There are no required URL params

  **Optional:**

  There are no optional URL params

* **Body Data**

  Must be sent as JSON with the correct headers

  **Required:**

    ```json
    {
      "title": "String",
      "author": "string",
      "genre_id": integer
    }
    ```

  **Optional:**

    ```json
    {
      "blurb": "string",
      "image": "url",
      "year": 1234
    }
    ```

  **Example:**

  `/api/books`

* **Success Response:**

    * **Code:** 201 CREATED <br />
      **Content:** <br />

  ```json
  {"message": "Book created"}
  ```

    * **Error Response:**

        * **Code:** 500 INTERNAL SERVER ERROR <br />
          **Content:** `{"message": "Unexpected error occurred"}`

        * **Code:** 422 UNPROCESSABLE CONTENT <br />
          **Content:**
        ```json
        {
            "message": "The title field is required. (and 2 more errors)",
            "errors": {
                "title": [
                  "The title field is required."
                ],
                "author": [
                  "The author field is required."
                ],
                "genre_id": [
                  "The genre id field is required."
                ]
            }
        }
        ```

### Delete a book

* **URL**

  /api/books/{id}

* **Method:**

  `DELETE`

* **URL Params**

  **Required:**

  There are no required URL params

  **Optional:**

  There are no optional URL params

* **Body Data**

  Must be sent as JSON with the correct headers

  **Required:**

  There are no required body parameters

  **Optional:**

  There are no required body parameters

  **Example:**

  `/api/books/1`

* **Success Response:**

    * **Code:** 200 OK <br />
      **Content:** <br />

  ```json
  {"message": "Book 1 was deleted"}
  ```

    * **Error Response:**

        * **Code:** 404 NOT FOUND <br />
          **Content:** `{"message": "The title field is required. (and 2 more errors)"}`


### Add a new book review

* **URL**

  /api/reviews

* **Method:**

  `POST`

* **URL Params**

  **Required:**

  There are no required URL params

  **Optional:**

  There are no optional URL params

* **Body Data**

  Must be sent as JSON with the correct headers

  **Required:**

    ```json
    {
      "name": "String",
      "rating": integer,
      "review": "string",
      "book_id": "int"
    }
    ```
  Note: rating must be an integer between 0 and 5
*
**Optional:**

There are no optional body parameters

**Example:**

`/api/reviews`

* **Success Response:**

    * **Code:** 201 CREATED <br />
      **Content:** <br />

  ```json
  {"message": "Review created"}
  ```

* **Error Response:**

    * **Code:** 500 INTERNAL SERVER ERROR <br />
      **Content:** `{"message": "Unexpected error occurred"}`

    * **Code:** 422 UNPROCESSABLE CONTENT <br />
      **Content:**
    ```json
      {      
          "message": "The name field is required. (and 3 more errors)",
          "errors": {
              "name": [
                "The name field is required."
              ],
              "rating": [
                "The rating field is required."
              ],
              "review": [
                "The review field is required."
              ],
              "book_id": [
                "The book id field is required."
              ]
          }
      }
    ```

### Return all genres

* **URL**

  /api/genres

* **Method:**

  `GET`

* **URL Params**

  **Required:**

  There are no required URL params

  **Optional:**

  There are no optional URL params

  **Example:**

  `/api/genres`

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** <br />

```json
{
    "data": [
        {
            "id": 1,
            "name": "Action"
        }
    ],
    "message": "Genres retrieved"
}
```

* **Error Response:**

    * **Code:** 500 INTERNAL SERVER ERROR <br />
      **Content:** `{"message": "Unexpected error occurred"}`

## Example fetch request

```js
fetch('https://book-swap-api.dev.io-academy.uk/api/books', {
    mode: 'cors',
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
    }
}).then(res => res.json())
    .then(data => {
        console.log(data)
    })

```
